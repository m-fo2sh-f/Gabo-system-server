<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Arr;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class TransactionService
{
    public function getAllTransactions(Request $request): LengthAwarePaginator
    {
        return Transaction::with(['client', 'employee', 'task'])
            ->when($request->filled('type'), function ($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->where('category', $request->category);
            })
            ->latest()
            ->paginate(15);
    }

    public function getTransactionById(int $id): Transaction
    {
        return Transaction::with(['client', 'employee', 'task'])->findOrFail($id);
    }

    public function createTransaction(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $transaction = Transaction::create($data);

            // 1. مصروف مرتبط بتاسك → نزيد التكلفة
            if ($transaction->type === 'expense' && $transaction->task_id && $transaction->category === 'task_payment') {
                Task::findOrFail($transaction->task_id)->increment('cost', $transaction->amount);
            }

            // 2. إيراد مرتبط بتاسك → نشوف لو اتكملت الدفعات
            if ($transaction->type === 'income' && $transaction->task_id && $transaction->category === 'task_payment') {
                $task = Task::findOrFail($transaction->task_id);

                $totalPaid = Transaction::where('task_id', $task->id)
                    ->where('type', 'income')
                    ->where('category', 'task_payment')
                    ->sum('amount');

                if ($totalPaid >= $task->price) {
                    $task->update(['status' => 'completed']);
                }

                // فلوس التاسك لا تؤثر على مديونية العميل أو الاشتراك
                return $transaction;
            }

            // 3. إيراد عام / اشتراك مرتبط بعميل (مش task_payment)
            if ($transaction->type === 'income' && !empty($data['client_id']) && $transaction->category !== 'task_payment') {
                $client = Client::findOrFail($data['client_id']);
                $paidAmount = $transaction->amount;

                if ($client->is_late) {
                    $remainingPayment = $paidAmount - $client->late_amount;

                    if ($remainingPayment <= 0) {
                        // سدد جزء أو كل المديونية بالظبط
                        $client->update([
                            'late_amount' => abs($remainingPayment),
                            'is_late'     => $remainingPayment < 0,
                        ]);
                    } else {
                        // سدد المديونية وفاضت فلوس → Overpayment
                        $client->update([
                            'late_amount' => 0,
                            'is_late'     => false,
                        ]);

                        // لو عنده قيمة اشتراك، احسب كام دورة تغطي الزيادة
                        if ($client->contract_value && $client->contract_value > 0) {
                            $cyclesCovered = (int) floor($remainingPayment / $client->contract_value);

                            if ($cyclesCovered > 0) {
                                $currentDate = Carbon::parse($client->next_payment_date ?? now());
                                for ($i = 0; $i < $cyclesCovered; $i++) {
                                    $currentDate = Carbon::parse(
                                        $this->calculateNextPaymentDate($currentDate, $client->payment_cycle)
                                    );
                                }
                                $client->update(['next_payment_date' => $currentDate->toDateString()]);
                            }
                        }
                    }
                } else {
                    // عميل منتظم → جدد ميعاد الدفع القادم
                    $client->update([
                        'next_payment_date' => $this->calculateNextPaymentDate(
                            $client->next_payment_date,
                            $client->payment_cycle
                        ),
                    ]);
                }
            }

            return $transaction;
        });
    }

    public function updateTransaction(int $id, array $data): Transaction
    {
        $transaction = Transaction::findOrFail($id);

        if (isset($data['amount']) && (float) $data['amount'] !== (float) $transaction->amount) {
            throw new \Exception('غير مسموح بتعديل قيمة المعاملة المالية بعد حفظها. يرجى حذفها وتسجيلها من جديد.');
        }

        $safeData = Arr::except($data, ['amount', 'type', 'client_id']);
        $transaction->update($safeData);

        return $transaction;
    }

    public function deleteTransaction(int $id): Transaction
    {
        return DB::transaction(function () use ($id) {
            $transaction = Transaction::findOrFail($id);

            // 1. لو إيراد عام مرتبط بعميل → نرجع المبلغ كمديونية
            if ($transaction->type === 'income' && $transaction->client_id && $transaction->category !== 'task_payment') {
                $client = Client::findOrFail($transaction->client_id);
                $newLateAmount = $client->late_amount + $transaction->amount;
                $client->update([
                    'late_amount' => $newLateAmount,
                    'is_late'     => $newLateAmount > 0,
                ]);
            }

            // 2. لو إيراد task_payment → نعيد تقييم حالة التاسك
            if ($transaction->type === 'income' && $transaction->task_id && $transaction->category === 'task_payment') {
                $task = Task::findOrFail($transaction->task_id);

                // إعادة حساب المجموع بعد الحذف (لا نحذف بعد, نحسب ما سيتبقى)
                $remainingPaid = Transaction::where('task_id', $task->id)
                    ->where('type', 'income')
                    ->where('category', 'task_payment')
                    ->where('id', '!=', $transaction->id) // نستثني المعاملة الحالية
                    ->sum('amount');

                if ($task->status === 'completed' && $remainingPaid < $task->price) {
                    $task->update(['status' => 'pending']);
                }
            }

            // 3. لو مصروف task_payment → ننقص التكلفة
            if ($transaction->type === 'expense' && $transaction->task_id && $transaction->category === 'task_payment') {
                Task::findOrFail($transaction->task_id)->decrement('cost', $transaction->amount);
            }

            $transaction->delete();

            return $transaction;
        });
    }

    public function calculateNextPaymentDate($startDate, $cycle): ?string
    {
        if (!$startDate) {
            return null;
        }

        $date = Carbon::parse($startDate);

        return match ($cycle) {
            'weekly'   => $date->addWeek()->toDateString(),
            'monthly'  => $date->addMonthNoOverflow()->toDateString(),
            'one_time' => null,
            default    => null,
        };
    }
}

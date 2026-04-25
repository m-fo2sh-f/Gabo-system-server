<?php


namespace App\Services;

use App\Models\TaskType;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\Task;
use App\Models\Client;
use App\Models\Employee;
use App\Models\JobTitle;

class DashboardService
{
    // 1. إحصائيات الدخل (بترجع Array فيها الرقم ونسبة التغير)
    public function getTotalIncome(): array
    {
        $currentMonthStart = Carbon::now()->copy()->startOfMonth();
        $currentMonthEnd   = Carbon::now()->copy()->endOfMonth();

        $lastMonth      = Carbon::now()->subMonth();
        $lastMonthStart = $lastMonth->copy()->startOfMonth();
        $lastMonthEnd   = $lastMonth->copy()->endOfMonth();

        $currentIncome = Transaction::where('type', 'income')
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');

        $lastIncome = Transaction::where('type', 'income')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        if ($lastIncome == 0) {
            $percentageChange = $currentIncome > 0 ? 100 : 0;
        } else {
            $percentageChange = (($currentIncome - $lastIncome) / $lastIncome) * 100;
        }

        return [
            'total_income' => (float) $currentIncome,
            'percentage_change' => round($percentageChange, 1),
            'is_positive' => $percentageChange >= 0
        ];
    }

    // 2. إجمالي المصروفات
    public function getTotalExpense(): array
    {
        $currentMonthStart = Carbon::now()->copy()->startOfMonth();
        $currentMonthEnd   = Carbon::now()->copy()->endOfMonth();

        $lastMonth      = Carbon::now()->subMonth();
        $lastMonthStart = $lastMonth->copy()->startOfMonth();
        $lastMonthEnd   = $lastMonth->copy()->endOfMonth();

        $currentExpense = Transaction::where('type', 'expense')
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');

        $lastExpense = Transaction::where('type', 'expense')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        if ($lastExpense == 0) {
            $percentageChange = $currentExpense > 0 ? 100 : 0;
        } else {
            $percentageChange = (($currentExpense - $lastExpense) / $lastExpense) * 100;
        }

        return [
            'total_expense' => (float) $currentExpense,
            'percentage_change' => round($percentageChange, 1),
            'is_positive' => $percentageChange <= 0
        ];
    }

    // 3. صافي الربح
    public function netProfit(): array
    {
        $currentMonthStart = Carbon::now()->copy()->startOfMonth();
        $currentMonthEnd   = Carbon::now()->copy()->endOfMonth();

        $lastMonth      = Carbon::now()->subMonth();
        $lastMonthStart = $lastMonth->copy()->startOfMonth();
        $lastMonthEnd   = $lastMonth->copy()->endOfMonth();

        $currentIncome = Transaction::where('type', 'income')
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');
        $currentExpense = Transaction::where('type', 'expense')
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');
        $currentProfit = $currentIncome - $currentExpense;

        $lastIncome = Transaction::where('type', 'income')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');
        $lastExpense = Transaction::where('type', 'expense')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');
        $lastProfit = $lastIncome - $lastExpense;

        if ($lastProfit == 0) {
            $percentageChange = $currentProfit > 0 ? 100 : ($currentProfit < 0 ? -100 : 0);
        } else {
            $percentageChange = (($currentProfit - $lastProfit) / abs($lastProfit)) * 100;
        }

        return [
            'net_profit' => (float) $currentProfit,
            'percentage_change' => round($percentageChange, 1),
            'is_positive' => $percentageChange >= 0
        ];
    }

    // 4. التاسكات النشطة
    public function getActiveTasks(): int
    {
        return Task::where('status', 'pending')->count();
    }

    // 5. إحصائيات الشهور للرسم البياني (Area Chart)
    public function getMonthlyStats(): array
    {
        $currentYear = Carbon::now()->year;
        $transactions = Transaction::selectRaw('
                MONTH(transaction_date) as month_num,
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expense
            ')
            ->whereYear('transaction_date', $currentYear)
            ->groupBy('month_num')
            ->get()
            ->keyBy('month_num');

        $monthlyData = [];
    
        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::create()->month($month)->format('M'); 
            
            $monthlyData[] = [
                'name'    => $monthName,
                'income'  => isset($transactions[$month]) ? (float) $transactions[$month]->total_income : 0,
                'expense' => isset($transactions[$month]) ? (float) $transactions[$month]->total_expense : 0,
            ];
        }

        return $monthlyData;
    }

    // 6. تقسيم المصروفات للرسم البياني الدائري (Pie Chart)
    public function getExpenseSources()
    {
        return Transaction::selectRaw('category as name, SUM(amount) as value')
            ->where('type', 'expense')
            ->whereNotNull('amount')
            ->groupBy('category')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'value' => (float) $item->value
                ];
            });
    }

    // 7. عدد العملاء
    public function getNumberOfClients(): int
    {
        return Client::count();
    }

    // 8. المديونيات المتأخرة
    public function getLateAmountMoney(): array
    {
        $totalAmount = Client::sum('late_amount');
        $clientsCount = Client::where('late_amount', '>', 0)->count();

        return [
            'total_amount' => (float) $totalAmount,
            'clients_count' => $clientsCount
        ];
    }

    // 9. أفضل 3 موظفين (بناءً على إجمالي أسعار التاسكات المرتبطة بيهم)
    public function getTopEmployees()
    {
        return Employee::withSum(['tasks' => function ($query) {
                $query->where('status', '!=', 'cancelled');
            }], 'price')
            ->get()
            ->filter(function ($employee) {
                return $employee->tasks_sum_price > 0;
            })
            ->sortByDesc('tasks_sum_price')
            ->take(3)
            ->map(function ($employee) {
                return [
                    'name' => $employee->name,
                    'total_income' => (float) $employee->tasks_sum_price
                ];
            })
            ->values();
    }

    // 10. أكثر 4 خدمات ربحاً (Bar Chart)
    public function getTopServices()
    {
        return TaskType::withSum(['tasks' => function ($query) {
        $query->where('status', '!=', 'cancelled');
    }], 'price')
    ->groupBy('task_types.id') 
    ->having('tasks_sum_price', '>', 0) 
    ->orderByDesc('tasks_sum_price') 
    ->take(4) 
    ->get()
            ->map(function ($service) { 
                return [
                    'name' => $service->name,
                    'revenue' => (float) $service->tasks_sum_price
                ];
            });
    }

    // 11. أحدث المعاملات (Table)
    public function getRecentTransactions()
    {
        return Transaction::with(['client', 'employee'])->latest()->take(5)->get()->map(function ($trx) {
            return [
                'id' => $trx->id,
                'type' => $trx->type,
                'category' => $trx->category,
                'amount' => (float) $trx->amount,
                'date' => $trx->transaction_date ? Carbon::parse($trx->transaction_date)->format('M d, Y') : ($trx->created_at ? $trx->created_at->format('M d, Y') : ''),
                'related_to' => $trx->client ? $trx->client->name : ($trx->employee ? $trx->employee->name : '')
            ];
        });
    }

    // ==========================================
    // دوال إضافية سبتهالك عشان لو محتاجها في حتة تانية
    // ==========================================
    
    public function getTotalTransactions(): int
    {
        return Transaction::count();
    }

    public function getTotalTasks(): int
    {
        return Task::count();
    }

    public function getTotalEmployees(): int
    {
        return Employee::count();
    }

    public function getTotalJobTitles(): int
    {
        return JobTitle::count();
    }

    public function getIncomeSources()
    {
        // ✅ الصح
return Transaction::selectRaw('
        CASE 
            WHEN transactions.category = "task_payment" THEN task_types.name 
            ELSE transactions.category 
        END as name,
        SUM(transactions.amount) as value
')
->leftJoin('tasks', 'transactions.task_id', '=', 'tasks.id') 
->leftJoin('task_types', 'tasks.task_type_id', '=', 'task_types.id') 
->where('transactions.type', 'income') 
->whereNotNull('transactions.amount') 
->groupByRaw('
        CASE 
            WHEN transactions.category = "task_payment" THEN task_types.name 
            ELSE transactions.category 
        END
') // 👈 التجميع يتم بالشرط الفعلي مش بالاسم المستعار
->get();
    }
}

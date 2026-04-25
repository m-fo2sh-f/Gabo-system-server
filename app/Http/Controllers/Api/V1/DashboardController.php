<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DashboardService;
use App\Traits\ApiResponseTrait;

class DashboardController extends Controller
{
    use ApiResponseTrait;
   
    public function __construct(private readonly DashboardService $dashboardService) {}
    
    public function index()
    {
        return $this->successResponse([
            'totalIncome' => $this->dashboardService->getTotalIncome(),
            'totalExpense' => $this->dashboardService->getTotalExpense(),
            'netProfit' => $this->dashboardService->netProfit(),
            'activeTasks' => $this->dashboardService->getActiveTasks(),
            'monthlyStats' => $this->dashboardService->getMonthlyStats(),
            'expenseSources' => $this->dashboardService->getExpenseSources(),
            'numberOfClients' => $this->dashboardService->getNumberOfClients(),
            'lateAmountMoney' => $this->dashboardService->getLateAmountMoney(),
            'topEmployees' => $this->dashboardService->getTopEmployees(),
            'topServices' => $this->dashboardService->getTopServices(),
            'recentTransaction' => $this->dashboardService->getRecentTransactions(),
            'totalTransactions' => $this->dashboardService->getTotalTransactions(),
            'totalTasks' => $this->dashboardService->getTotalTasks(),
            'totalEmployees' => $this->dashboardService->getTotalEmployees(),
            'totalJobTitles' => $this->dashboardService->getTotalJobTitles(),
            'incomeSources' => $this->dashboardService->getIncomeSources(),
        ], 'Dashboard stats fetched successfully');
    }
}

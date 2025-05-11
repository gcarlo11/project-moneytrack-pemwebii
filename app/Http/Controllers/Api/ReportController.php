<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Get total income and expense
        $totalIncome = Transaction::whereHas('category', function($query) {
            $query->where('type', 'income');
        })
        ->whereBetween('transaction_date', [$startDate, $endDate])
        ->sum('amount');
        
        $totalExpense = Transaction::whereHas('category', function($query) {
            $query->where('type', 'expense');
        })
        ->whereBetween('transaction_date', [$startDate, $endDate])
        ->sum('amount');
        
        // Get transactions by category
        $incomeByCategory = Category::where('type', 'income')
            ->withSum(['transactions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('transaction_date', [$startDate, $endDate]);
            }], 'amount')
            ->having('transactions_sum_amount', '>', 0)
            ->get();
            
        $expenseByCategory = Category::where('type', 'expense')
            ->withSum(['transactions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('transaction_date', [$startDate, $endDate]);
            }], 'amount')
            ->having('transactions_sum_amount', '>', 0)
            ->get();
            
        // Get daily summary
        $dailySummary = [];
        $startDateCarbon = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);
        $currentDate = clone $startDateCarbon;
        
        while ($currentDate <= $endDateCarbon) {
            $date = $currentDate->format('Y-m-d');
            
            $dailyIncome = Transaction::whereHas('category', function($query) {
                $query->where('type', 'income');
            })
            ->whereDate('transaction_date', $date)
            ->sum('amount');
            
            $dailyExpense = Transaction::whereHas('category', function($query) {
                $query->where('type', 'expense');
            })
            ->whereDate('transaction_date', $date)
            ->sum('amount');
            
            $dailySummary[] = [
                'date' => $date,
                'income' => $dailyIncome,
                'expense' => $dailyExpense,
                'balance' => $dailyIncome - $dailyExpense
            ];
            
            $currentDate->addDay();
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'net_balance' => $totalIncome - $totalExpense,
                'income_by_category' => $incomeByCategory,
                'expense_by_category' => $expenseByCategory,
                'daily_summary' => $dailySummary
            ]
        ]);
    }
}

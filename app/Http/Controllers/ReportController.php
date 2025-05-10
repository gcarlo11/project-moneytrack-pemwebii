<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Convert to Carbon instances for calculations
        $startDateCarbon = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);
        
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
        
        return view('reports.index', compact(
            'startDate', 
            'endDate', 
            'totalIncome', 
            'totalExpense',
            'incomeByCategory',
            'expenseByCategory',
            'dailySummary'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Get today's date
        $today = Carbon::today();
        
        // Calculate daily totals
        $dailyIncome = Transaction::whereHas('category', function($query) {
            $query->where('type', 'income');
        })
        ->whereDate('transaction_date', $today)
        ->sum('amount');
        
        $dailyExpense = Transaction::whereHas('category', function($query) {
            $query->where('type', 'expense');
        })
        ->whereDate('transaction_date', $today)
        ->sum('amount');
        
        // Last 7 days - create a full array of all 7 days
        $startOfWeek = Carbon::today()->subDays(6);
        $endOfWeek = Carbon::today();
        
        // Initialize weekly data array with all dates in the week
        $weeklyData = [];
        $current = clone $startOfWeek;
        
        while ($current <= $endOfWeek) {
            $formattedDate = $current->format('Y-m-d');
            $weeklyData[$formattedDate] = [
                'date' => $current->format('d M'),
                'income' => 0,
                'expense' => 0
            ];
            $current->addDay();
        }
        
        // Get transactions for the last 7 days
        $transactions = Transaction::with('category')
            ->whereDate('transaction_date', '>=', $startOfWeek)
            ->whereDate('transaction_date', '<=', $today)
            ->get();
            
        // Fill in the weekly data with actual transactions
        foreach ($transactions as $transaction) {
            $date = $transaction->transaction_date->format('Y-m-d');
            if ($transaction->category->type === 'income') {
                $weeklyData[$date]['income'] += $transaction->amount;
            } else {
                $weeklyData[$date]['expense'] += $transaction->amount;
            }
        }
        
        // For compatibility with existing code, still get the grouped transactions
        $weeklyTransactions = $transactions->groupBy(function ($transaction) {
            return $transaction->transaction_date->format('Y-m-d');
        });
            
        // Calculate weekly totals
        $weeklyIncome = Transaction::whereHas('category', function($query) {
            $query->where('type', 'income');
        })
        ->whereDate('transaction_date', '>=', $startOfWeek)
        ->whereDate('transaction_date', '<=', $today)
        ->sum('amount');
        
        $weeklyExpense = Transaction::whereHas('category', function($query) {
            $query->where('type', 'expense');
        })
        ->whereDate('transaction_date', '>=', $startOfWeek)
        ->whereDate('transaction_date', '<=', $today)
        ->sum('amount');
        
        // Monthly totals
        $startOfMonth = Carbon::today()->startOfMonth();
        $monthlyIncome = Transaction::whereHas('category', function($query) {
            $query->where('type', 'income');
        })
        ->whereDate('transaction_date', '>=', $startOfMonth)
        ->whereDate('transaction_date', '<=', $today)
        ->sum('amount');
        
        $monthlyExpense = Transaction::whereHas('category', function($query) {
            $query->where('type', 'expense');
        })
        ->whereDate('transaction_date', '>=', $startOfMonth)
        ->whereDate('transaction_date', '<=', $today)
        ->sum('amount');
        
        // Recent transactions for dashboard
        $recentTransactions = Transaction::with('category')
            ->orderBy('transaction_date', 'desc')
            ->limit(5)
            ->get();
            
        return view('dashboard', compact(
            'dailyIncome', 
            'dailyExpense', 
            'weeklyIncome', 
            'weeklyExpense', 
            'monthlyIncome', 
            'monthlyExpense',
            'weeklyTransactions',
            'weeklyData',
            'recentTransactions'
        ));
    }
}

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
        
        // Last 7 days transactions for weekly view
        $startOfWeek = Carbon::today()->subDays(6);
        $weeklyTransactions = Transaction::with('category')
            ->whereDate('transaction_date', '>=', $startOfWeek)
            ->whereDate('transaction_date', '<=', $today)
            ->orderBy('transaction_date', 'desc')
            ->get()
            ->groupBy(function ($transaction) {
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
            'recentTransactions'
        ));
    }
}

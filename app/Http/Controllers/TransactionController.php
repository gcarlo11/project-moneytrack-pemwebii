<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->input('filter', 'all');
        $dateRange = $request->input('date_range', 'this_month');
        
        $query = Transaction::with('category');
        
        // Filter by transaction type
        if ($filter === 'income') {
            $query->whereHas('category', function($q) {
                $q->where('type', 'income');
            });
        } elseif ($filter === 'expense') {
            $query->whereHas('category', function($q) {
                $q->where('type', 'expense');
            });
        }
        
        // Filter by date range
        if ($dateRange === 'today') {
            $query->whereDate('transaction_date', Carbon::today());
        } elseif ($dateRange === 'this_week') {
            $query->whereBetween('transaction_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($dateRange === 'this_month') {
            $query->whereBetween('transaction_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        } elseif ($dateRange === 'custom' && $request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('transaction_date', [$request->input('start_date'), $request->input('end_date')]);
        }
        
        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(15);
        
        return view('transactions.index', compact('transactions', 'filter', 'dateRange'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('transactions.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);
        
        Transaction::create($validated);
        
        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully');
    }

    public function show(Transaction $transaction): View
    {
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction): View
    {
        $categories = Category::all();
        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);
        
        $transaction->update($validated);
        
        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->delete();
        
        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully');
    }
}

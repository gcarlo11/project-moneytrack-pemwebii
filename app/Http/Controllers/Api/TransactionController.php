<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
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
        
        $transactions = $query->orderBy('transaction_date', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);
        
        $transaction = Transaction::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully',
            'data' => $transaction->load('category')
        ]);
    }

    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);
        
        $transaction->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully',
            'data' => $transaction->load('category')
        ]);
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Transaction deleted successfully'
        ]);
    }
}

@extends('layouts.app')

@section('title', 'Transactions')
@section('header', 'Transactions')

@section('actions')
    <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus"></i> New Transaction
    </a>
@endsection

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('transactions.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="filter" class="form-label">Transaction Type</label>
                    <select name="filter" id="filter" class="form-select">
                        <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All Transactions</option>
                        <option value="income" {{ $filter === 'income' ? 'selected' : '' }}>Income Only</option>
                        <option value="expense" {{ $filter === 'expense' ? 'selected' : '' }}>Expense Only</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="date_range" class="form-label">Date Range</label>
                    <select name="date_range" id="date_range" class="form-select">
                        <option value="today" {{ $dateRange === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="this_week" {{ $dateRange === 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ $dateRange === 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="custom" {{ $dateRange === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>
                
                <div class="col-md-4" id="custom-date-range" style="{{ $dateRange !== 'custom' ? 'display: none;' : '' }}">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                                <td>
                                    <span class="badge rounded-pill" style="background-color: {{ $transaction->category->color }}">
                                        <i class="fas {{ $transaction->category->icon }}"></i>
                                        {{ $transaction->category->name }}
                                    </span>
                                </td>
                                <td>{{ $transaction->description ?: '-' }}</td>
                                <td class="{{ $transaction->category->type === 'income' ? 'text-success' : 'text-danger' }} fw-bold">
                                    {{ $transaction->category->type === 'income' ? '+' : '-' }}Rp{{ number_format($transaction->amount, 0, ',', '.') }}
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this transaction?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No transactions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateRangeSelect = document.getElementById('date_range');
        const customDateRange = document.getElementById('custom-date-range');
        
        dateRangeSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateRange.style.display = 'block';
            } else {
                customDateRange.style.display = 'none';
            }
        });
    });
</script>
@endsection

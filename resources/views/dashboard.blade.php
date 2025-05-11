@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('actions')
    <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus"></i> New Transaction
    </a>
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Today</h5>
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-0">Income</p>
                            <h3 class="text-success">Rp{{ number_format($dailyIncome, 0, ',', '.') }}</h3>
                        </div>
                        <div>
                            <p class="mb-0">Expense</p>
                            <h3 class="text-danger">Rp{{ number_format($dailyExpense, 0, ',', '.') }}</h3>
                        </div>
                        <div>
                            <p class="mb-0">Balance</p>
                            <h3 class="{{ $dailyIncome - $dailyExpense >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp{{ number_format($dailyIncome - $dailyExpense, 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">This Week</h5>
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-0">Income</p>
                            <h3 class="text-success">Rp{{ number_format($weeklyIncome, 0, ',', '.') }}</h3>
                        </div>
                        <div>
                            <p class="mb-0">Expense</p>
                            <h3 class="text-danger">Rp{{ number_format($weeklyExpense, 0, ',', '.') }}</h3>
                        </div>
                        <div>
                            <p class="mb-0">Balance</p>
                            <h3 class="{{ $weeklyIncome - $weeklyExpense >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp{{ number_format($weeklyIncome - $weeklyExpense, 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">This Month</h5>
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-0">Income</p>
                            <h3 class="text-success">Rp{{ number_format($monthlyIncome, 0, ',', '.') }}</h3>
                        </div>
                        <div>
                            <p class="mb-0">Expense</p>
                            <h3 class="text-danger">Rp{{ number_format($monthlyExpense, 0, ',', '.') }}</h3>
                        </div>
                        <div>
                            <p class="mb-0">Balance</p>
                            <h3 class="{{ $monthlyIncome - $monthlyExpense >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp{{ number_format($monthlyIncome - $monthlyExpense, 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Weekly Recap</h5>
                </div>
                <div class="card-body">
                    <canvas id="weeklyChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Transactions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse ($recentTransactions as $transaction)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge rounded-pill" style="background-color: {{ $transaction->category->color }}">
                                        <i class="fas {{ $transaction->category->icon }}"></i>
                                        {{ $transaction->category->name }}
                                    </span>
                                    <div>{{ $transaction->description }}</div>
                                    <small class="text-muted">{{ $transaction->transaction_date->format('d M Y') }}</small>
                                </div>
                                <span class="{{ $transaction->category->type === 'income' ? 'text-success' : 'text-danger' }} fw-bold">
                                    {{ $transaction->category->type === 'income' ? '+' : '-' }}Rp{{ number_format($transaction->amount, 0, ',', '.') }}
                                </span>
                            </div>
                        @empty
                            <div class="list-group-item text-center">No recent transactions</div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-primary">View All Transactions</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prepare data for weekly chart
        const dates = [];
        const incomeData = [];
        const expenseData = [];
        
        @foreach($weeklyTransactions as $date => $transactions)
            dates.push('{{ \Carbon\Carbon::parse($date)->format("d M") }}');
            
            let dailyIncome = 0;
            let dailyExpense = 0;
            
            @foreach($transactions as $transaction)
                @if($transaction->category->type == 'income')
                    dailyIncome += {{ $transaction->amount }};
                @else
                    dailyExpense += {{ $transaction->amount }};
                @endif
            @endforeach
            
            incomeData.push(dailyIncome);
            expenseData.push(dailyExpense);
        @endforeach
        
        // Create weekly chart
        const ctx = document.getElementById('weeklyChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Income',
                        data: incomeData,
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Expense',
                        data: expenseData,
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection

@extends('layouts.app')

@section('title', 'Financial Reports')
@section('header', 'Financial Reports')

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('reports.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                </div>
                
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Income</h5>
                    <h2 class="mb-0">Rp{{ number_format($totalIncome, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Total Expense</h5>
                    <h2 class="mb-0">Rp{{ number_format($totalExpense, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-white {{ $totalIncome - $totalExpense >= 0 ? 'bg-info' : 'bg-warning' }}">
                <div class="card-body">
                    <h5 class="card-title">Net Balance</h5>
                    <h2 class="mb-0">Rp{{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Income by Category</h5>
                </div>
                <div class="card-body">
                    <canvas id="incomeChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Expense by Category</h5>
                </div>
                <div class="card-body">
                    <canvas id="expenseChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Daily Summary</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Income</th>
                            <th>Expense</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailySummary as $summary)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($summary['date'])->format('d M Y') }}</td>
                                <td class="text-success">Rp{{ number_format($summary['income'], 0, ',', '.') }}</td>
                                <td class="text-danger">Rp{{ number_format($summary['expense'], 0, ',', '.') }}</td>
                                <td class="{{ $summary['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    Rp{{ number_format($summary['balance'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Income by category chart
        const incomeCtx = document.getElementById('incomeChart').getContext('2d');
        new Chart(incomeCtx, {
            type: 'pie',
            data: {
                labels: [
                    @foreach($incomeByCategory as $category)
                        '{{ $category->name }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($incomeByCategory as $category)
                            {{ $category->transactions_sum_amount }},
                        @endforeach
                    ],
                    backgroundColor: [
                        @foreach($incomeByCategory as $category)
                            '{{ $category->color }}',
                        @endforeach
                    ],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${context.label}: Rp${value.toLocaleString('id-ID')} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        // Expense by category chart
        const expenseCtx = document.getElementById('expenseChart').getContext('2d');
        new Chart(expenseCtx, {
            type: 'pie',
            data: {
                labels: [
                    @foreach($expenseByCategory as $category)
                        '{{ $category->name }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($expenseByCategory as $category)
                            {{ $category->transactions_sum_amount }},
                        @endforeach
                    ],
                    backgroundColor: [
                        @foreach($expenseByCategory as $category)
                            '{{ $category->color }}',
                        @endforeach
                    ],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${context.label}: Rp${value.toLocaleString('id-ID')} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection


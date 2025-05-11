@extends('layouts.app')

@section('title', 'Transaction Details')
@section('header', 'Transaction Details')

@section('actions')
    <div class="btn-group">
        <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-edit"></i> Edit
        </a>
        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this transaction?')">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4 text-center">
                        <span class="badge fs-5 rounded-pill" style="background-color: {{ $transaction->category->color }}">
                            <i class="fas {{ $transaction->category->icon }}"></i>
                            {{ $transaction->category->name }}
                        </span>
                        <h2 class="mt-3 {{ $transaction->category->type === 'income' ? 'text-success' : 'text-danger' }}">
                            {{ $transaction->category->type === 'income' ? '+' : '-' }}Rp{{ number_format($transaction->amount, 0, ',', '.') }}
                        </h2>
                        <p class="text-muted">{{ $transaction->transaction_date->format('d M Y') }}</p>
                    </div>
                    
                    @if($transaction->description)
                        <div class="mb-4">
                            <h5>Description</h5>
                            <p>{{ $transaction->description }}</p>
                        </div>
                    @endif
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Created: {{ $transaction->created_at->format('d M Y H:i') }}</span>
                        <span>Updated: {{ $transaction->updated_at->format('d M Y H:i') }}</span>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Transactions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

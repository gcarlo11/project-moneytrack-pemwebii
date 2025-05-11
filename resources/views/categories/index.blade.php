@extends('layouts.app')

@section('title', 'Categories')
@section('header', 'Categories')

@section('actions')
    <a href="{{ route('categories.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus"></i> New Category
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Income Categories</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse ($categories->where('type', 'income') as $category)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge rounded-pill" style="background-color: {{ $category->color }}">
                                        <i class="fas {{ $category->icon }}"></i>
                                    </span>
                                    {{ $category->name }}
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this category?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center">No income categories found</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Expense Categories</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse ($categories->where('type', 'expense') as $category)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge rounded-pill" style="background-color: {{ $category->color }}">
                                        <i class="fas {{ $category->icon }}"></i>
                                    </span>
                                    {{ $category->name }}
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this category?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center">No expense categories found</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

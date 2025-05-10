<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:255',
        ]);
        
        Category::create($validated);
        
        return redirect()->route('categories.index')->with('success', 'Category created successfully');
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:255',
        ]);
        
        $category->update($validated);
        
        return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Check if category has transactions
        if ($category->transactions()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Cannot delete category with transactions');
        }
        
        $category->delete();
        
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Income categories
        Category::create(['name' => 'Salary', 'type' => 'income', 'color' => '#4CAF50', 'icon' => 'fa-money-bill']);
        Category::create(['name' => 'Investment', 'type' => 'income', 'color' => '#2196F3', 'icon' => 'fa-chart-line']);
        Category::create(['name' => 'Gifts', 'type' => 'income', 'color' => '#9C27B0', 'icon' => 'fa-gift']);
        
        // Expense categories
        Category::create(['name' => 'Food', 'type' => 'expense', 'color' => '#F44336', 'icon' => 'fa-utensils']);
        Category::create(['name' => 'Transportation', 'type' => 'expense', 'color' => '#FF9800', 'icon' => 'fa-car']);
        Category::create(['name' => 'Utilities', 'type' => 'expense', 'color' => '#795548', 'icon' => 'fa-bolt']);
        Category::create(['name' => 'Shopping', 'type' => 'expense', 'color' => '#E91E63', 'icon' => 'fa-shopping-bag']);
    }
}

<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Categories - Maitighar')]
class CategoriesPage extends Component
{
    public function render(): View
    {
        $categories = Category::query()
            ->where('is_active', 1)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return view('livewire.categories-page', compact('categories'));
    }
}

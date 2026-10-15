<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Category;
use App\Models\HomeSlider;
use App\Models\Product;
use Illuminate\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Maitighar - Nepalese Heritage with Love')]
class HomePage extends Component
{
    use LivewireAlert;

    public function addToCart(int $product_id): void
    {
        $total_count = CartManagement::addItemToCart($product_id);

        $this->dispatch('update-cart-count', total_count: $total_count);

        $this->dispatch('product-added-to-cart', product_id: $product_id, quantity: 1);

        $this->alert('success', 'Product added to your bag.', [
            'position' => 'top-end',
            'timer' => 2500,
            'toast' => true,
        ]);
    }

    public function render(): View
    {
        $sliders = HomeSlider::active()->get();

        $featured_categories = Category::query()
            ->where('is_active', true)
            ->whereHas('products')
            ->latest('id')
            ->take(5)
            ->get();

        $new_arrivals = Product::query()
            ->with(['brand', 'category'])
            ->withAvg('approvedReviews', 'rating')
            ->where('is_active', true)
            ->latest()
            ->take(6)
            ->get();

        $sale_products = Product::query()
            ->with(['brand', 'category'])
            ->withAvg('approvedReviews', 'rating')
            ->where('is_active', true)
            ->where('on_sale', true)
            ->latest()
            ->take(4)
            ->get();

        return view('livewire.home-page', compact('sliders', 'new_arrivals', 'sale_products', 'featured_categories'));
    }
}
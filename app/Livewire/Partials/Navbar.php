<?php

namespace App\Livewire\Partials;

use App\Helpers\CartManagement;
use App\Models\Category;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Navbar extends Component
{
    public int $total_count = 0;

    public int $wishlist_count = 0;

    public string $search = '';

    public string $selectedCategory = '';

    public function mount(): void
    {
        $this->total_count = CartManagement::getTotalQuantity();
        $this->refreshWishlistCount();
        $this->search = (string) request('search', '');
    }

    #[Computed]
    public function categories(): array
    {
        return Cache::remember('navigation.categories', now()->addHour(), fn (): array => Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug'])
            ->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ])
            ->all());
    }

    public function submitSearch()
    {
        $params = [];
        if (filled($this->search)) {
            $params['search'] = trim($this->search);
        }
        if (filled($this->selectedCategory)) {
            $params['selected_categories'] = [$this->selectedCategory];
        }

        return redirect()->route('all-products', $params);
    }

    #[On('update-cart-count')]
    public function updateCartCount(?int $total_count = null): void
    {
        $this->total_count = $total_count ?? CartManagement::getTotalQuantity();
    }

    #[On('update-wishlist-count')]
    public function updateWishlistCount(): void
    {
        $this->refreshWishlistCount();
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('index');
    }

    public function render(): View
    {
        return view('livewire.partials.navbar', [
            'categories' => $this->categories,
            'total_count' => $this->total_count,
            'wishlist_count' => $this->wishlist_count,
        ]);
    }

    private function refreshWishlistCount(): void
    {
        $this->wishlist_count = Auth::check()
            ? Wishlist::where('user_id', Auth::id())->count()
            : 0;
    }
}

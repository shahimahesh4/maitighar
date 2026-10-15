<?php

namespace App\Livewire;

use App\Models\Order;
use App\Support\OrderPresentation;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('My Orders - Maitighar')]
class MyOrdersPage extends Component
{
    use WithPagination;

    public function mount()
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }
    }

    public function render(): View
    {
        $baseQuery = Order::query()->where('user_id', auth()->id());
        $orders = (clone $baseQuery)->latest()->paginate(10);
        $summary = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'delivered' => (clone $baseQuery)->where('status', 'delivered')->count(),
            'spent' => (clone $baseQuery)->where('payment_status', 'paid')->sum('grand_total'),
        ];

        return view('livewire.my-orders-page', [
            'orders' => $orders,
            'summary' => $summary,
            'orderPresentation' => OrderPresentation::class,
        ]);
    }
}

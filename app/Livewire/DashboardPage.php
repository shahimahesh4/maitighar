<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('My Dashboard - Maitighar')]
class DashboardPage extends Component
{
    public function render(): View
    {
        $user = auth()->user()->load('trainer');
        $orders = Order::query()
            ->where('user_id', $user->id)
            ->withCount('items')
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.dashboard-page', [
            'user' => $user,
            'orders' => $orders,
            'trainer' => $user->trainer,
            'roleLabel' => User::roleOptions()[$user->role] ?? ucfirst($user->role),
            'stats' => [
                'orders' => $user->orders()->count(),
                'active_orders' => $user->orders()->whereNotIn('status', ['delivered', 'cancelled'])->count(),
                'wishlist' => $user->wishlists()->count(),
                'spent' => $user->orders()->where('payment_status', 'paid')->sum('grand_total'),
            ],
        ]);
    }
}

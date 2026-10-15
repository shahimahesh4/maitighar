<?php

namespace App\Livewire;

use App\Models\Order;
use App\Support\OrderPresentation;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Order Details - Maitighar')]
class MyOrderDetailPage extends Component
{
    public $order;

    public function mount($order)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $this->order = Order::with('address', 'items.product')->where('user_id', auth()->id())->where('id', $order)->firstOrFail();
    }

    public function render(): View
    {
        return view('livewire.my-order-detail-page', [
            'orderStatusClass' => OrderPresentation::statusClass($this->order->status),
            'paymentStatusClass' => OrderPresentation::paymentStatusClass($this->order->payment_status),
            'trackingSteps' => OrderPresentation::trackingSteps($this->order->status),
        ]);
    }
}

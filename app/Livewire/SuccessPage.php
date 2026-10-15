<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Success - Maitighar')]
class SuccessPage extends Component
{
    #[Url]
    public $order_id;

    public $order;

    public function mount()
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if (! $this->order_id) {
            return redirect()->route('index');
        }

        $this->order = Order::with('address', 'items.product')
            ->where('user_id', auth()->id())
            ->findOrFail($this->order_id);
    }

    public function render(): View
    {
        return view('livewire.success-page');
    }
}

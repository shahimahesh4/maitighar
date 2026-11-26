<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\QuoteRequest;
use App\Services\CommunicationService;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Request a Quote | Maitighar')]
class QuoteRequestPage extends Component
{
    public Product $product;
    public string $full_name = '';
    public string $email = '';
    public string $phone = '';
    public string $city = '';
    public string $address = '';
    public int $quantity = 1;
    public string $message = '';

    public function mount(Product $product): void
    {
        abort_unless($product->is_active && $product->in_stock, 404);
        $this->product = $product;
        if (auth()->check()) {
            $this->full_name = (string) auth()->user()->name;
            $this->email = (string) auth()->user()->email;
            $this->phone = (string) (auth()->user()->phone ?? '');
            $this->city = (string) (auth()->user()->city ?? '');
            $this->address = (string) (auth()->user()->address ?? '');
        }
    }

    public function submit(): void
    {
        $data = $this->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:500'],
            'quantity' => ['required', 'integer', 'min:1', 'max:9999'],
            'message' => ['nullable', 'string', 'max:3000'],
        ]);

        $quote = QuoteRequest::create([...$data, 'product_id' => $this->product->id]);
        $details = ['Product' => $this->product->name, 'SKU' => $this->product->sku ?: '—', 'Quantity' => (string) $quote->quantity, 'Customer' => $quote->full_name, 'Email' => $quote->email, 'Phone' => $quote->phone, 'City' => $quote->city, 'Address' => $quote->address];
        $communications = app(CommunicationService::class);
        $communications->notifyQuoteAdmins('New quote request: '.$this->product->name, 'New product quote request', 'A customer requested a price quote.', $details, url('/stnapanel'));
        $communications->send($quote->email, 'We received your quote request', 'Your quote request is received', 'Thank you. Our team will prepare a quote and contact you shortly.', $details, null, null, $quote->message);
        $this->reset('quantity', 'message');
        $this->quantity = 1;
        session()->flash('quote_success', 'Your quote request has been sent. Our team will contact you soon.');
    }

    public function render(): View
    {
        return view('livewire.quote-request-page');
    }
}

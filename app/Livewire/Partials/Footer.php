<?php

namespace App\Livewire\Partials;

use App\Models\Subscriber;
use App\Models\Setting;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

class Footer extends Component
{
    public string $subscriberEmail = '';

    public function subscribe(): void
    {
        $key = 'footer-subscribe:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 4)) {
            $this->addError('subscriberEmail', 'Please wait a moment before trying again.');

            return;
        }

        $validated = $this->validate(['subscriberEmail' => ['required', 'email:rfc', 'max:255']]);
        $email = Str::lower(trim($validated['subscriberEmail']));

        Subscriber::updateOrCreate(['email' => $email], ['is_active' => true]);
        RateLimiter::hit($key, 60);
        $this->reset('subscriberEmail');
        session()->flash('footer_subscribed', 'Thank you for subscribing.');
    }

    public function render(): View
    {
        return view('livewire.partials.footer', [
            'trustItems' => $this->trustItems(),
            'exploreLinks' => $this->exploreLinks(),
            'supportLinks' => $this->supportLinks(),
            'contactItems' => $this->contactItems(),
            'socialLinks' => $this->socialLinks(),
        ]);
    }

    private function trustItems(): array
    {
        return [
            ['icon' => 'bi-patch-check', 'title' => 'Authentic Heritage', 'text' => 'Nepalese heritage, culture & craftsmanship'],
            ['icon' => 'bi-box-seam', 'title' => 'Delivery with Care', 'text' => 'Carefully packaged heritage collections'],
            ['icon' => 'bi-headset', 'title' => 'Friendly support', 'text' => 'Dedicated help with orders and inquiries'],
        ];
    }

    private function exploreLinks(): array
    {
        return [
            ['label' => 'Shop products', 'url' => route('all-products')],
            ['label' => 'Training & Courses', 'url' => route('pages.show', 'academy-training')],
            ['label' => 'Photo gallery', 'url' => route('gallery.index')],
            ['label' => 'Video gallery', 'url' => route('videos.index')],
            ['label' => 'About Maitighar', 'url' => route('pages.show', 'about-us')],
        ];
    }

    private function supportLinks(): array
    {
        $accountLinks = auth()->check()
            ? [
                ['label' => 'My dashboard', 'url' => route('dashboard')],
                ['label' => 'Orders & tracking', 'url' => route('my-orders')],
            ]
            : [
                ['label' => 'Sign in to account', 'url' => route('login')],
                ['label' => 'Create an account', 'url' => route('register')],
            ];

        return [
            ...$accountLinks,
            ['label' => 'My wishlist', 'url' => route('wishlist')],
            ['label' => 'Refund & Return Policy', 'url' => route('pages.show', 'refund-return-policy')],
            ['label' => 'Contact support', 'url' => route('contact')],
        ];
    }

    private function contactItems(): array
    {
        $address = Setting::get('store_address', '2/75 Dartbrook Road, Auburn NSW, Sydney, NSW 2144');
        $phone = Setting::get('store_phone', '+61 432 685 546');
        $email = Setting::get('store_email', 'maitigharaustralia@gmail.com');

        return [
            ['icon' => 'bi-geo-alt', 'label' => 'Find us', 'value' => $address],
            ['icon' => 'bi-telephone', 'label' => 'Call us', 'value' => $phone, 'url' => 'tel:'.preg_replace('/[^+\d]/', '', $phone)],
            ['icon' => 'bi-envelope', 'label' => 'Email us', 'value' => $email, 'url' => 'mailto:'.$email],
        ];
    }

    private function socialLinks(): array
    {
        return [
            ['label' => 'Facebook', 'icon' => 'bi-facebook', 'url' => Setting::get('facebook_url', 'https://www.facebook.com/maitigharaustralia')],
            ['label' => 'Instagram', 'icon' => 'bi-instagram', 'url' => Setting::get('instagram_url', 'https://www.instagram.com/maitighar')],
            ['label' => 'TikTok', 'icon' => 'bi-tiktok', 'url' => 'https://www.tiktok.com'],
        ];
    }
}

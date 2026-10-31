<?php

namespace App\Livewire;

use App\Models\ContactMessage;
use App\Models\Page;
use App\Models\Setting;
use App\Services\CommunicationService;
use App\Traits\GoogleRecaptcha;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Contact Us | Maitighar')]
class ContactPage extends Component
{
    use GoogleRecaptcha;

    public string $full_name = '';

    public string $email = '';

    public string $phone = '';

    public string $address = '';

    public string $subject = '';

    public string $message = '';

    public string $website = '';

    public function mount(): void
    {
        if (auth()->check()) {
            $this->full_name = (string) auth()->user()->name;
            $this->email = (string) auth()->user()->email;
            $this->phone = (string) (auth()->user()->phone ?? '');
        }
    }

    public function submit(): void
    {
        if ($this->website !== '') {
            $this->resetForm();
            session()->flash('contact_success', 'Thank you. Your message has been received.');

            return;
        }

        if (! $this->verifyRecaptcha()) {
            return;
        }

        $key = 'contact-form:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 4)) {
            $this->addError('message', 'Too many messages were sent. Please wait a minute and try again.');

            return;
        }

        $this->phone = $this->normalizePhone($this->phone);

        $rules = array_merge($this->getRecaptchaRules(), [
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'regex:/^(\+614\d{8}|\+9779[78]\d{8})$/'],
            'address' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        $validated = $this->validate($rules, [
            'phone.regex' => 'Enter a valid mobile number (e.g. 0412 345 678 or +977 9812345678).',
        ]);

        $contactMessage = ContactMessage::create($validated);
        RateLimiter::hit($key, 60);

        $communications = app(CommunicationService::class);
        $details = ['Name' => $contactMessage->full_name, 'Email' => $contactMessage->email, 'Phone' => $contactMessage->phone, 'Subject' => $contactMessage->subject];
        $communications->notifyAdmin('Website enquiry: '.$contactMessage->subject, 'New contact enquiry', 'A visitor submitted the website contact form.', $details, url('/stnapanel'));
        if ($communications->receiptsEnabled()) {
            $communications->send($contactMessage->email, 'We received your message', 'Message received', 'Thank you for contacting Maitighar. Our team will reply as soon as possible.', $details, null, null, $contactMessage->message);
        }

        $this->resetForm();
        session()->flash('contact_success', 'Thank you! Your message has been sent. Our team will contact you soon.');
    }

    private function normalizePhone(string $phone): string
    {
        $raw = trim($phone);
        $digits = preg_replace('/\D+/', '', $raw);

        // Australian mobile: 04XXXXXXXX, 4XXXXXXXX, 614XXXXXXXX
        if (str_starts_with($digits, '614') && strlen($digits) === 11) {
            return '+'.$digits;
        }

        if (str_starts_with($digits, '04') && strlen($digits) === 10) {
            return '+61'.substr($digits, 1);
        }

        if (str_starts_with($digits, '4') && strlen($digits) === 9) {
            return '+61'.$digits;
        }

        // Nepal mobile: 97798XXXXXXXX, 98XXXXXXXX, 97XXXXXXXX
        if (str_starts_with($digits, '977')) {
            $digits = substr($digits, 3);
        }

        if ((str_starts_with($digits, '98') || str_starts_with($digits, '97')) && strlen($digits) === 10) {
            return '+977'.$digits;
        }

        return str_starts_with($raw, '+') ? '+'.$digits : '+'.$digits;
    }

    private function resetForm(): void
    {
        $this->reset(['full_name', 'email', 'phone', 'address', 'subject', 'message', 'website']);
    }

    public function render(): View
    {
        $contactPage = Page::query()->where('slug', 'contact-us')->first();

        return view('livewire.contact-page', [
            'contactBannerUrl' => $contactPage?->getBannerUrl() ?? asset('images/kba/about-us-academy.png'),
            'storeEmail' => Setting::get('store_email', 'maitigharaustralia@gmail.com'),
            'storePhone' => Setting::get('store_phone', '+61 432 685 546'),
            'storeAddress' => Setting::get('store_address', '2/75 Dartbrook Road, Auburn NSW, Sydney, NSW 2144'),
        ]);
    }
}

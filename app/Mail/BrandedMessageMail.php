<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BrandedMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectLine,
        public string $heading,
        public string $intro,
        public array $details = [],
        public ?string $actionLabel = null,
        public ?string $actionUrl = null,
        public ?string $messageText = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                Setting::get('email_from_name', 'Maitighar'),
            ),
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.branded');
    }
}

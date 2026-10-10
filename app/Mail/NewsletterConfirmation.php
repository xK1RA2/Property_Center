<?php

namespace App\Mail;

use App\Models\NewsletterSubscriber;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewsletterConfirmation extends Mailable
{
    public function __construct(public NewsletterSubscriber $subscriber)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Confirm your ' . config('app.name') . ' newsletter subscription');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter-confirm',
            with: ['confirmUrl' => $this->subscriber->confirmUrl()],
        );
    }
}

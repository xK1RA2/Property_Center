<?php

namespace App\Mail;

use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

/** One issue of the newsletter. Queued, so a large list does not tie up the command or the web server. */
class NewsletterIssue extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public NewsletterSubscriber $subscriber,
        public string $subjectLine,
        public string $body,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter-issue',
            with: [
                'body' => $this->body,
                'unsubscribeUrl' => $this->subscriber->unsubscribeUrl(),
            ],
        );
    }

    /** Lets Gmail/Outlook show their own "Unsubscribe" button, which also protects deliverability. */
    public function headers(): Headers
    {
        return new Headers(text: [
            'List-Unsubscribe' => '<' . $this->subscriber->unsubscribeUrl() . '>',
        ]);
    }
}

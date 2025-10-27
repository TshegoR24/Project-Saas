<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Subscription $subscription,
        public int $daysUntilDue
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Reminder: ' . $this->subscription->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-reminder',
            with: [
                'user' => $this->user,
                'subscription' => $this->subscription,
                'daysUntilDue' => $this->daysUntilDue,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

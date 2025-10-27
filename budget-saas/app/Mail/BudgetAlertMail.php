<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Budget;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BudgetAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Budget $budget,
        public float $currentSpending,
        public float $percentage
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Budget Alert: ' . $this->budget->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.budget-alert',
            with: [
                'user' => $this->user,
                'budget' => $this->budget,
                'currentSpending' => $this->currentSpending,
                'percentage' => $this->percentage,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

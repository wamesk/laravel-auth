<?php

declare(strict_types=1);

namespace Wame\LaravelAuth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserEmailVerificationByLinkMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected Model $user,
        protected string $verificationLink
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('laravel-auth::emails.verificationLink.subject')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'wame-auth::emails.users.verification_link',
            with: ['verificationLink' => $this->verificationLink]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}

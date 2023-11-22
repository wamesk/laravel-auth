<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Mail;

use App\Models\User;
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

    /**
     * @param User|Model $user
     * @param string $verificationLink
     */
    public function __construct(
        protected User|Model $user,
        protected string $verificationLink
    ) {}

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __(key: 'emails.verificationLink.subject')
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'wame-auth::emails.users.verificationLink',
            with: ['verificationLink' => $this->verificationLink]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}

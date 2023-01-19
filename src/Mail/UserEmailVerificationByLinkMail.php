<?php

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
    use Queueable, SerializesModels;

    /** @var User|Model $user */
    protected User|Model $user;

    /** @var string $verificationLink */
    protected string $verificationLink;

    /**
     * @param User|Model $user
     * @param string $verificationLink
     */
    public function __construct(User|Model $user, string $verificationLink)
    {
        $this->user = $user;
        $this->verificationLink = $verificationLink;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Verify your email address')
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
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

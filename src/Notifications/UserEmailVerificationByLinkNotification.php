<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Wame\LaravelAuth\Mail\UserEmailVerificationByLinkMail;

class UserEmailVerificationByLinkNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param string $verificationLink
     */
    public function __construct(
        protected string $verificationLink
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param Model $notifiable
     * @return array
     */
    public function via(Model $notifiable): array
    {
        return ['mail'];
    }

    /**
     * @param Model $notifiable
     * @return UserEmailVerificationByLinkMail
     */
    public function toMail(Model $notifiable): UserEmailVerificationByLinkMail
    {
        return (new UserEmailVerificationByLinkMail($notifiable, $this->verificationLink))->to($notifiable);
    }

    /**
     * @param Model $notifiable
     * @param $channel
     * @return bool
     */
    public function shouldSend(Model $notifiable, $channel): bool
    {
        return !$notifiable->hasVerifiedEmail();
    }
}

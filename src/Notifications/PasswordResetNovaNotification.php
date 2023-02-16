<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Wame\LaravelAuth\Mail\UserPasswordResetNovaMail;

class PasswordResetNovaNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     */
    public function __construct(
        protected string $token
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * @param $notifiable
     * @return UserPasswordResetNovaMail
     */
    public function toMail($notifiable): UserPasswordResetNovaMail
    {
        return (new UserPasswordResetNovaMail($notifiable, $this->token))->to($notifiable);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [

        ];
    }
}

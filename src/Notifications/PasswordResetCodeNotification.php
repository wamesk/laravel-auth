<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Wame\LaravelAuth\Mail\UserPasswordResetCodeMail;

class PasswordResetCodeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param string $code
     */
    public function __construct(
        protected string $code
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
     * Get the mail representation of the notification.
     *
     * @param $notifiable
     * @return UserPasswordResetCodeMail
     */
    public function toMail($notifiable): UserPasswordResetCodeMail
    {
        return (new UserPasswordResetCodeMail($notifiable, $this->code))->to($notifiable);
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

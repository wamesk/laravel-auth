<?php

declare(strict_types = 1);

namespace Wame\LaravelAuth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Wame\LaravelAuth\Mail\UserRegisteredMail;

class UserRegisteredNotification extends Notification
{
    use Queueable;

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
     * @return UserRegisteredMail
     */
    public function toMail($notifiable): UserRegisteredMail
    {
        return (new UserRegisteredMail($notifiable))->to($notifiable);
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

    /**
     * Determine if the notification should be sent.
     *
     * @param $notifiable
     * @param $channel
     * @return bool
     */
    public function shouldSend($notifiable, $channel): bool
    {
        return !$notifiable->hasVerifiedEmail();
    }
}

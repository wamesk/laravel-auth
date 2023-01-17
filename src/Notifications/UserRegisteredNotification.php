<?php

namespace Wame\LaravelAuth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Wame\LaravelAuth\Mail\UserRegisteredMail;

class UserRegisteredNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
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
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    /**
     * Determine if the notification should be sent.
     *
     * @param $notifiable
     * @param $channel
     * @return bool
     */
    public function shouldSend($notifiable, $channel)
    {
        return !$notifiable->hasVerifiedEmail();
    }
}

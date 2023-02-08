<?php

namespace Wame\LaravelAuth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Wame\LaravelAuth\Mail\UserPasswordResetCodeMail;
use Wame\LaravelAuth\Mail\UserPasswordResetNovaMail;

class PasswordResetNovaNotification extends Notification
{
    use Queueable;

    /** @var string  */
    protected string $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $token)
    {
        $this->token = $token;
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
     * @return UserPasswordResetNovaMail
     */
    public function toMail($notifiable)
    {
        return (new UserPasswordResetNovaMail($notifiable, $this->token))->to($notifiable);
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
}

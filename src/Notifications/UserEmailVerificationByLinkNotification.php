<?php

namespace Wame\LaravelAuth\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Wame\LaravelAuth\Mail\UserEmailVerificationByLinkMail;

class UserEmailVerificationByLinkNotification extends Notification
{
    use Queueable;

    /** @var string $verificationLink */
    protected string $verificationLink;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $verificationLink)
    {
        $this->verificationLink = $verificationLink;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  Model  $notifiable
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
     * @param User $notifiable
     * @param $channel
     * @return bool
     */
    public function shouldSend(User $notifiable, $channel): bool
    {
        return !$notifiable->hasVerifiedEmail();
    }
}

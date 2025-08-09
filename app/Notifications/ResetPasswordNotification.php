<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    private string $token, $email;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->from(env('MAIL_FROM_ADDRESS', 'noreply@apotekdigital.com'), 'Apotek Digital')
            ->subject('Permintaan Reset Password')
            ->greeting('Halo!')
            ->line('Kami menerima permintaan reset password untuk akun Anda.')
            ->line('Silakan klik tombol di bawah untuk mereset password Anda. Tautan ini hanya berlaku selama **60 menit**.')
            ->action(
                'Reset Password',
                route('auth.reset-password.form', ['token' => $this->token]) . '?email=' . urlencode($this->email)
            )
            ->line('Jika Anda tidak meminta reset password, abaikan email ini. Akun Anda tetap aman.')
            ->salutation('Salam, **Tim Apotek Digital**');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

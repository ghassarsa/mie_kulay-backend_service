<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // URL FE yang akan menampilkan form reset password
        $frontendUrl = "http://localhost:5173/login?token={$this->token}&email={$notifiable->email}";


        return (new MailMessage)
            ->subject('Reset Password Anda')
            ->line('Klik tombol di bawah untuk mengubah password Anda.')
            ->action('Ubah Password', $frontendUrl)
            ->line('Jika Anda tidak meminta reset password, abaikan email ini.');
    }
}

<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeAndVerifyEmail extends VerifyEmailBase
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $appName = config('app.name');

        return (new MailMessage)
            // ইমেইলের বিষয় পরিবর্তন
            ->subject('Welcome to ' . $appName . '! Please Verify Your Email')
            
            // ব্যবহারকারীর নাম দিয়ে ব্যক্তিগত সম্ভাষণ
            ->greeting('Hi ' . $notifiable->name . ',')

            // সূচনা বার্তা
            ->line('Welcome to Remind me! We are excited to have you on board.')
            ->line('To complete your registration and secure your account, please click the button below.')

            // বাটনের লেখা এবং লিঙ্ক
            ->action('Activate My Account', $verificationUrl)
            
            // শেষের বার্তা
            ->line('If you did not create an account, you can safely ignore this email.')
            ->line('Thank you for choosing us!');
    }
}
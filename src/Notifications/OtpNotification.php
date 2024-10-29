<?php

namespace Nksquare\LaravelOtp\Notifications;

use Illuminate\Bus\Queueable;
use Nksquare\LaravelOtp\Facades\Otp;
use Nksquare\LaravelOtp\Mail\OtpMail;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Nksquare\LaravelOtp\Channels\SmsChannel;
use Nksquare\LaravelOtp\Messages\OtpMessage;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;

class OtpNotification extends Notification
{
    use Queueable;
    
    protected string $code;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->code = Otp::generateCode();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [SmsChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $email = $notifiable instanceof AnonymousNotifiable
            ? $notifiable->routeNotificationFor('mail')
            : $notifiable->email;

        Otp::put($email, $this->code);

        return new OtpMail($this->code);
    }

    public function toSms($notifiable)
    {
        $phoneNo = $notifiable instanceof AnonymousNotifiable
            ? $notifiable->routeNotificationFor('sms')
            : $notifiable->phone_no;
        
        Otp::put($phoneNo, $this->code);
        
        return new OtpMessage($this->code);
    }
}

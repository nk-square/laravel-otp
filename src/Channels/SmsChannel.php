<?php
 
namespace Nksquare\LaravelOtp\Channels;
 
use Nksquare\LaravelSms\Facades\Sms;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        $message = $notification->toSms($notifiable);

        Sms::send($message);
    }
}
<?php

namespace Nksquare\LaravelOtp\Sms;

use Nksquare\LaravelSms\Facades\Sms as LaravelSms;
use Nksquare\LaravelSms\Message;

class Sms implements SmsInterface
{
    /**
     * @param $phoneNo string
     * @param $message string
     */
    public function send($phoneNo,$message)
    {
        $msg = new Message();
        $msg->setMessage($message);
        $msg->setRecipient($phoneNo);
        LaravelSms::send($msg);
    }
}
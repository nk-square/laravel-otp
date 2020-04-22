<?php

namespace Nksquare\LaravelOtp\Sms;

interface SmsInterface {
    /**
     * @param $phoneNo string
     * @param $message string
     */
    public function send($phoneNo,$message);
}
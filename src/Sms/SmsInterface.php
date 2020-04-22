<?php

namespace Nksquare\LaravelOtp\Sms;

interface SmsInterface {
	public function send($number,$message);
}
<?php

namespace Nksquare\LaravelOtp\Messages;

use Nksquare\LaravelSms\Message;

class OtpMessage extends Message
{
	public function __construct($code)
	{
		$this->setMessage($code.' is your OTP. Do not share it with anyone.');
	}
}
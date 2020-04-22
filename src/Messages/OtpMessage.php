<?php

namespace Nksquare\LaravelOtp\Messages;

use Nksquare\LaravelSms\Message;

class OtpMessage extends Message
{
	public function __construct($title,$code)
	{
		$this->setMessage('Your OTP for '.$title.' is '.$code);
	}
}
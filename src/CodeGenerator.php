<?php

namespace Nksquare\LaravelOtp;

class CodeGenerator
{
	/**
	 * generate otp code
	 * @return string
	 */
	public function generate($length)
	{
		return mt_rand('1'.str_repeat('0',$length-1),str_repeat('9',$length));
	}
}
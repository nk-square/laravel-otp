<?php

namespace Nksquare\LaravelOtp\Storage;

use Illuminate\Support\Facades\Session;

class SessionStorage implements StorageInterface
{
	public function put($recipient,$code,$expire)
	{
		Session::put("otp.$recipient",[
			'code' => $code,
			'expire' => $expire,
		]);
	}

	public function get($recipient)
	{
		return Session::get("otp.$recipient");
	}

	public function clearOtp($recipient)
	{
		Session::forget("otp.$recipient");
	}
}
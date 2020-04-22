<?php

namespace Nksquare\LaravelOtp;

use Illuminate\Contracts\Mail\Mailer;
use Nksquare\LaravelOtp\Mail\OtpMail;
use Nksquare\LaravelOtp\Sms\SmsInterface;
use Nksquare\LaravelOtp\Storage\StorageInterface;
use Carbon\Carbon;

class Otp
{
	protected $sms;

	protected $storage;

	function __construct(SmsInterface $sms,Mailer $mailer,StorageInterface $storage) 
	{
		$this->sms = $sms;
		$this->storage = $storage;
		$this->mailer = $mailer;
		$this->code = new CodeGenerator();
	}

	public function generate($recipient)
	{
		$otp = $this->OTPGenerator->generate();

		$this->storage->put($recipient,$otp);

		return $otp;
	}

	protected function getExpiryTime()
	{
		return Carbon::now()->addSeconds(config('otp.ttl'));
	}

	public function sms($phoneNo,$message)
	{
		$code = $this->code->generate(config('otp.length'));

		$this->storage->put($phoneNo,$code,$this->getExpiryTime());

		$message = str_replace(':code',$code,$message);

		$this->sms->send($phoneNo,$message);
	}

	public function email($email,$mailable=null)
	{
		$code = $this->code->generate(config('otp.length'));

		$mailable = $mailable ?? new OtpMail($code);

		$mailable->code = $code;

		$this->storage->put($email,$code,$this->getExpiryTime());

		$this->mailer->to($email)->send($mailable);
	}

	public function verify($recipient,$code)
	{
		$otp = $this->storage->get($recipient);

		return  $otp && $otp['code'] == $code && $otp['expire']->greaterThan(Carbon::now());
	}

	public function clearOtp($recipient)
	{
		$this->storage->clear($recipient);
	}
}
<?php

namespace Nksquare\LaravelOtp;

use Illuminate\Contracts\Mail\Mailer;
use Nksquare\LaravelOtp\Mail\OtpMail;
use Nksquare\LaravelOtp\Sms\SmsInterface;
use Nksquare\LaravelOtp\Storage\StorageInterface;
use Carbon\Carbon;

class Otp
{
    /**
     * @var \Nksquare\LaravelOtp\Sms\SmsInterface
     */
    protected $sms;

    /**
     * @var \Illuminate\Contracts\Mail\Mailer
     */
    protected $mailer;

    /**
     * @var \Nksquare\LaravelOtp\Storage\StorageInterface
     */
    protected $storage;

    /**
     * @var \Nksquare\LaravelOtp\CodeGenerator
     */
    protected $code;

    /**
     * @param $sms \Nksquare\LaravelOtp\Sms\SmsInterface
     * @param $mailer \Illuminate\Contracts\Mail\Mailer
     * @param $storage \Nksquare\LaravelOtp\Storage\StorageInterface
     */
    function __construct(SmsInterface $sms,Mailer $mailer,StorageInterface $storage) 
    {
        $this->sms = $sms;
        $this->storage = $storage;
        $this->mailer = $mailer;
        $this->code = new CodeGenerator();
    }

    /**
     * @return \Carbon\Carbon
     */
    protected function getExpiryTime()
    {
        return Carbon::now()->addSeconds(config('otp.ttl'));
    }

    /**
     * @param $phoneNo string
     * @param $message string
     */
    public function sms($phoneNo,$message)
    {
        $code = $this->code->generate(config('otp.length'));

        $this->storage->put($phoneNo,$code,$this->getExpiryTime());

        $message = str_replace(':code',$code,$message);

        $this->sms->send($phoneNo,$message);
    }

    /**
     * @param $email string
     * @param $mailable null|\Illuminate\Mail\Mailable
     */
    public function email($email,$mailable=null)
    {
        $code = $this->code->generate(config('otp.length'));

        $mailable = $mailable ?? new OtpMail($code);

        $mailable->code = $code;

        $this->storage->put($email,$code,$this->getExpiryTime());

        $this->mailer->to($email)->send($mailable);
    }

    /**
     * @param $recipient string
     * @param $code string
     * @return boolean
     */
    public function verify($recipient,$code)
    {
        $otp = $this->storage->get($recipient);

        return  $otp && $otp['code'] == $code && $otp['expire']->greaterThan(Carbon::now());
    }

    /**
     * @param $recipient string
     */
    public function clearOtp($recipient)
    {
        $this->storage->clear($recipient);
    }
}
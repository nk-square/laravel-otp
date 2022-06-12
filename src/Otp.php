<?php

namespace Nksquare\LaravelOtp;

use Illuminate\Contracts\Mail\Mailer;
use Nksquare\LaravelOtp\Mail\OtpMail;
use Nksquare\LaravelOtp\Sms\SmsInterface;
use Nksquare\LaravelOtp\Storage\StorageInterface;
use Psr\Log\LoggerInterface;
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
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $correctCode;

    /**
     * @var array
     */
    protected $attemptsIncreased = [];

    /**
     * @var string
     */
    protected $message;

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

        $this->_sms($phoneNo,$message,$code);
    }

    /**
     * @param $phoneNo string
     * @param $message string
     * @param $code string
     */
    public function _sms($phoneNo,$message,$code)
    {
        $this->storage->put($phoneNo,$code,$this->getExpiryTime());

        $this->message = str_replace(':code',$code,$message);

        $this->sms->send($phoneNo,$this->message);
    }

    /**
     * @param $email string
     * @param $mailable null|\Illuminate\Mail\Mailable
     */
    public function email($email,$mailable=null)
    {
        $code = $this->code->generate(config('otp.length'));

        $this->_email($email,$code,$mailable);
    }

    /**
     * @param $email string
     * @param $code string
     * @param $mailable null|\Illuminate\Mail\Mailable
     */
    public function _email($email,$code,$mailable=null)
    {
        $mailable = $mailable ?? new OtpMail($code);

        $mailable->code = $code;

        $this->storage->put($email,$code,$this->getExpiryTime());

        $this->mailer->to($email)->send($mailable);
    }

    /**
     * @param $recipient mixed
     * @param $code string
     * @return boolean
     */
    public function verify($recipients,$code)
    {
        $recipients = !is_array($recipients) ? [$recipients] : $recipients;

        foreach ($recipients as $recipient) 
        {
            $otpCode = $this->getOtpCode($recipient);

            if($otpCode && (string)$otpCode == (string)$code)
            {
                return true;
            }
            $this->increaseAttempts($recipient);
        } 
        return false;  
    }

    /**
     * @param $recipient mixed
     */
    public function clearOtp($recipients)
    {
        $recipients = !is_array($recipients) ? [$recipients] : $recipients;
        foreach ($recipients as $recipient) 
        {
            $this->storage->clear($recipient);
        }
    }

    /**
     * @param $channels array
     */
    public function send($channels)
    {
        $code = $this->code->generate(config('otp.length'));

        if($channels['sms'])
        {
            $this->_sms($channels['sms']['recipient'],$channels['sms']['message'],$code);
        }
        
        if($channels['email'])
        {
            $this->_email($channels['email']['recipient'],$code,$channels['email']['mailable']??null);
        }
    }

    /**
     * @param $recipient string
     * @return string
     */
    public function getOtpCode($recipient)
    {
        if(!isset($this->correctCode[$recipient]))
        {
            $this->correctCode[$recipient] = $this->storage->get($recipient);
        }

        $otp = $this->correctCode[$recipient];
        return $otp && $otp['expire']->greaterThan(Carbon::now()) ? $otp['code'] : null;
    }
    
    /**
     * @return string
     */
    public function humanReadableExpiry()
    {
        return ceil(config('otp.ttl')/60).' minutes';
    }

    /**
     * @return string
     */
    public function getAttempts($recipient)
    {
        return $this->storage->getAttempts($recipient);
    }

    /**
     * @return void
     */
    public function increaseAttempts($recipient)
    {
        if(!isset($this->attemptsIncreased[$recipient]))
        {
            $this->attemptsIncreased[$recipient] = true;
            $this->storage->increaseAttempts($recipient);
        }
    }

    /**
     * get the last sent sms message
     * @return string|null
     */
    public function getLastSms()
    {
        return $this->message;
    }
}
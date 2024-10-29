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
    protected SmsInterface $sms;

    /**
     * @var \Illuminate\Contracts\Mail\Mailer
     */
    protected Mailer $mailer;

    /**
     * @var \Nksquare\LaravelOtp\Storage\StorageInterface
     */
    protected StorageInterface $storage;

    /**
     * @var \Nksquare\LaravelOtp\CodeGenerator
     */
    protected CodeGenerator $code;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;

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

    public function generateCode() : string
    {
        return $this->code->generate(config('otp.length'));
    }

    protected function getExpiryTime() : Carbon
    {
        return Carbon::now()->addSeconds(config('otp.ttl'));
    }

    public function put(array|string $recipient,string $code) : void
    {
        $this->storage->put($recipient,$code,$this->getExpiryTime());
    }

    public function verify(array|string $recipients,string $code)
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
    public function clearOtp(array|string $recipients)
    {
        $recipients = !is_array($recipients) ? [$recipients] : $recipients;
        foreach ($recipients as $recipient) 
        {
            $this->storage->clear($recipient);
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
    public function humanReadableExpiry() : string
    {
        return ceil(config('otp.ttl')/60).' minutes';
    }

    /**
     * @return string
     */
    public function getAttempts(string|array $recipient) : ?int
    {
        $recipients = !is_array($recipient) ? [$recipient] : $recipient;
        $attempts = [];
        foreach($recipients as $recipient)
        {
            if($attempt = $this->storage->getAttempts($recipient))
            {
                $attempts[] = $attempt;
            }
        }
        return $attempts ? max($attempts) : null;
    }

    /**
     * @return void
     */
    public function increaseAttempts(array|string $recipient) : void
    {
        $recipients = !is_array($recipient) ? [$recipient] : $recipient;
        foreach($recipients as $recipient)
        {
            dump($recipient);
            $this->storage->increaseAttempts($recipient);
        }
    }
}
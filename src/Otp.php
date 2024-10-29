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
     * @param $sms \Nksquare\LaravelOtp\Sms\SmsInterface
     * @param $mailer \Illuminate\Contracts\Mail\Mailer
     * @param $storage \Nksquare\LaravelOtp\Storage\StorageInterface
     */
    function __construct(StorageInterface $storage) 
    {
        $this->storage = $storage;
        $this->code = new CodeGenerator();
    }
    
    protected function getExpiryTime() : Carbon
    {
        return Carbon::now()->addSeconds(config('otp.ttl'));
    }

    public function put(string|array $recipients,string $code) : void
    {
        $recipients = is_array($recipients) ? implode('|',$recipients) : $recipients;
        
        $this->storage->put($recipients,$code,$this->getExpiryTime());
    }
    
    public function verify(array|string $recipients,string $code) : bool
    {
        $recipients = is_array($recipients) ? $recipients : [$recipients];

        $otps = $this->storage->all();

        foreach($otps as $key => $otp)
        {
            $keys = explode('|',$key);
            
            if(count(array_intersect($recipients,$keys))>0)
            {
                if($otp['code']==$code && $otp['expire']->greaterThan(now()))
                {
                    return true;
                }
            }
        }
        return false;  
    }
    
    public function clearOtp(string|array $recipients) : void
    {
        $recipients = !is_array($recipients) ? [$recipients] : $recipients;
        foreach ($recipients as $recipient) 
        {
            $this->storage->clear($recipient);
        }
    }
    
    public function getOtpCode(string $recipient) : ?string
    {
        $otp = $this->storage->get($recipient);

        return is_array($otp) && $otp['expire']->greaterThan(now()) ? $otp['code'] : null;
    }
    
    public function humanReadableExpiry() : string
    {
        return ceil(config('otp.ttl')/60).' minutes';
    }

    public function getAttempts($recipient) : ?int
    {
        return $this->storage->getAttempts($recipient);
    }
    
    public function increaseAttempts($recipient) : void
    {
        $this->storage->increaseAttempts($recipient);
    }
}
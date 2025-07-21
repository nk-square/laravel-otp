<?php

namespace Nksquare\LaravelOtp;

use Nksquare\LaravelOtp\Storage\StorageInterface;
use Carbon\Carbon;

class Otp
{
    protected StorageInterface $storage;
    
    protected CodeGenerator $code;
    
    function __construct(StorageInterface $storage,CodeGenerator $code) 
    {
        $this->storage = $storage;
        $this->code = $code;
    }
    
    protected function getExpiryTime() : Carbon
    {
        return now()->addSeconds(config('otp.ttl'));
    }

    public function getOtp(array|string $recipients) : ?array
    {
        $recipients = is_array($recipients) ? $recipients : [$recipients];

        $otps = $this->storage->all();

        foreach($otps as $key => $otp)
        {
            $keys = explode('|',$key);
            
            if(count(array_intersect($recipients,$keys))>0 && now()->lessThanOrEqualTo($otp['expire']))
            {
                return $otp;
            }
        }
        return null;
    }

    public function generate(string|array $recipients) : string
    {
        $code = $this->code->generate(config('otp.length'));
        $this->put($recipients,$code);
        return $code;
    }

    public function put(string|array $recipients,string $code) : void
    {
        $recipients = is_array($recipients) ? implode('|',$recipients) : $recipients;
        
        $this->storage->put($recipients,$code,$this->getExpiryTime());
    }
    
    public function verify(array|string $recipients,?string $code) : bool
    {
        $otp = $this->getOtp($recipients);

        return $otp!==null && $otp['code'] == $code;
    }
    
    public function humanReadableExpiry() : string
    {
        return ceil(config('otp.ttl')/60).' minutes';
    }

    public function getAttempts(string|array $recipients) : ?int
    {
        if($otp = $this->getOtp($recipients))
        {
            return $otp['attempts'];
        }
        return null;
    }

    protected function resolveStorageKey(string|array $recipients) : ?string
    {
        $recipients = is_array($recipients) ? $recipients : [$recipients];

        $otps = $this->storage->all();

        foreach($otps as $key => $otp)
        {
            $keys = explode('|',$key);
            if(count(array_intersect($recipients,$keys))>0)
            {
                return $key;
            }
        }
        return null;
    }
    
    public function forget(string|array $recipients) : void
    {
        if($key = $this->resolveStorageKey($recipients))
        {
            $this->storage->forget($key);
        }
    }

    public function flush() : void
    {
        $this->storage->flush();
    }
    
    public function increaseAttempts(string|array $recipients) : void
    {
        if($key = $this->resolveStorageKey($recipients))
        {
            $this->storage->increaseAttempts($key);
        }
    }
}
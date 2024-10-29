<?php

namespace Nksquare\LaravelOtp\Storage;

use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class SessionStorage implements StorageInterface
{
    public function put(string $recipient,string $code,Carbon $expire) : void
    {
        Session::put("_otp.$recipient",[
            'code' => $code,
            'expire' => $expire,
            'attempts' => 0
        ]);
    }

    public function all() : array
    {
        return Session::get("_otp",[]);
    }

    public function get(string $recipient) : string
    {
        return Session::get("otp.$recipient");
    }

    public function clear(string $recipient) : void
    {
        Session::forget($recipient ? "otp.$recipient" : 'otp');
    }
    
    public function getAttempts(string $recipient) : ?int
    {
        return $this->get($recipient)['attempts'] ?? null;
    }
    
    public function increaseAttempts(string $recipient) : void
    {
        if($this->getAttempts($recipient)!==null)
        {
            Session::put("otp.$recipient.attempts",$this->getAttempts($recipient)+1);
        }
    }
}
<?php

namespace Nksquare\LaravelOtp\Storage;

use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class SessionStorage implements StorageInterface
{
    const ESCAPE = '#';

    protected function encode(string $string) : string
    {
        return str_replace('.',static::ESCAPE,$string);
    }

    protected function decode(string $string) : string
    {
        return str_replace(static::ESCAPE,'.',$string);
    }

    public function put(string $recipient,string $code,Carbon $expire) : void
    {
        Session::put("_otp.".$this->encode($recipient),[
            'code' => $code,
            'expire' => $expire,
            'attempts' => 0
        ]);
    }

    public function all() : array
    {
        return collect(Session::get("_otp",[]))->mapWithKeys(function($item,$key){
            return [$this->decode($key) => $item];
        })->toArray();
    }

    public function get(string $recipient) : ?array
    {
        return Session::get("_otp.".$this->encode($recipient));
    }

    public function forget(string $recipient) : void
    {
        Session::forget("_otp.".$this->encode($recipient));
    }
    
    public function increaseAttempts(string $recipient,int $count=1) : void
    {
        if($otp = $this->get($recipient))
        {
            Session::put("_otp.".$this->encode($recipient).".attempts",$otp['attempts']+1);
        }
    }

    public function flush() : void
    {
        Session::forget("_otp");
    }
}
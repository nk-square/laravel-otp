<?php

namespace Nksquare\LaravelOtp\Storage;

use Illuminate\Support\Facades\Session;

class SessionStorage implements StorageInterface
{
    /**
     * @param $recipient string
     * @param $code string
     * @param $expire \Carbon\Carbon
     */
    public function put($recipient,$code,$expire)
    {
        Session::put("otp.$recipient",[
            'code' => $code,
            'expire' => $expire,
            'attempts' => 0
        ]);
    }

    /**
     * @param $recipient string
     * @return array
     */
    public function get($recipient)
    {
        return Session::get("otp.$recipient");
    }

    /**
     * @param $recipient string
     */
    public function clear($recipient=null)
    {
        Session::forget($recipient ? "otp.$recipient" : 'otp');
    }

    /**
     * @param $recipient string
     */
    public function getAttempts($recipient)
    {
        return $this->get($recipient)['attempts'] ?? null;
    }

    /**
     * @param $recipient string
     */
    public function increaseAttempts($recipient)
    {
        if($this->getAttempts($recipient)!==null)
        {
            Session::put("otp.$recipient.attempts",$this->getAttempts($recipient)+1);
        }
    }
}
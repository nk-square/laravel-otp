<?php

namespace Nksquare\LaravelOtp\Storage;

interface StorageInterface 
{
    /**
     * @param $recipient string
     * @param $code string
     * @param $expire \Carbon\Carbon
     */
    public function put($recipient,$code,$expire);

    /**
     * @param $recipient string
     * @return array
     */
    public function get($recipient);

    /**
     * @param $recipient string
     */
    public function clear($recipient);

}
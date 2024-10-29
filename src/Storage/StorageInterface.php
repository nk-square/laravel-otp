<?php

namespace Nksquare\LaravelOtp\Storage;

use Carbon\Carbon;

interface StorageInterface 
{
    public function put(string $recipient,string $code,Carbon $expire) : void;

    public function get(string $recipient) : string;
    
    public function clear(string $recipient) : void;

    public function all() : array;

}
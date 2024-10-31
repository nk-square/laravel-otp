<?php

namespace Nksquare\LaravelOtp\Storage;

use Carbon\Carbon;

interface StorageInterface 
{
    public function put(string $recipient,string $code,Carbon $expire) : void;

    public function get(string $recipient) : ?array;
    
    public function forget(string $recipient) : void;

    public function all() : array;

    public function increaseAttempts(string $recipient,int $count=1) : void;

    public function flush() : void;

}
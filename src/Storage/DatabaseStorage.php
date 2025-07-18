<?php

namespace Nksquare\LaravelOtp\Storage;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseStorage implements StorageInterface
{
    public function put(string $recipient,string $code,Carbon $expire) : void
    {
        DB::table('otp')->insert([
            'recipient' => $recipient,
            'code' => $code,
            'expire' => $expire,
            'attempts' => 0
        ]);
    }

    public function all() : array
    {
        return DB::table('otp')->get()->mapWithKeys(function($item,$key){
            return [
                $item->recipient => [
                    'code' => $item->code,
                    'expire' => $item->expire,
                    'attempts' => $item->attempts
                ]
            ];
        })->toArray();
    }

    public function get(string $recipient) : ?array
    {
        $otp = DB::table('otp')->where('recipient',$recipient)->where('expire','<=',now()->toDateTimeString())->first();
        if($otp){
            return [
                'code' => $otp->code,
                'expire' => $otp->expire,
                'attempts' => $otp->attempts
            ];
        }
        return null;
    }

    public function forget(string $recipient) : void
    {
        DB::table('otp')->where('recipient',$recipient)->delete();
    }
    
    public function increaseAttempts(string $recipient,int $count=1) : void
    {
        DB::table('otp')->where('recipient',$recipient)->increment('attempts');
    }

    public function flush() : void
    {
        
    }
}
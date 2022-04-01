<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Otp length
    |--------------------------------------------------------------------------
    |
    | Length of the otp.
    |
    */
    'length' => 6,

    /*
    |--------------------------------------------------------------------------
    | Otp lifetime
    |--------------------------------------------------------------------------
    |
    | Lifetime of the otp in seconds
    |
    */
    'ttl' => 600,

    /*
    |--------------------------------------------------------------------------
    | Sms driver class
    |--------------------------------------------------------------------------
    |
    | If you want to use your own sms driver, replace this value with 
    | the full class path of your sms driver class. Your driver class
    | should implement \Nksquare\LaravelOtp\Sms\SmsInterface.
    |
    */
    'sms' => \Nksquare\LaravelOtp\Sms\Sms::class,

    /*
    |--------------------------------------------------------------------------
    | Storage class
    |--------------------------------------------------------------------------
    |
    | If you want to use your own storage driver, replace this value with 
    | the full class path of your storage driver class. Your storage class
    | should implement \Nksquare\LaravelOtp\Storage\StorageInterface
    |
    */
    'storage' => \Nksquare\LaravelOtp\Storage\SessionStorage::class,


    /*
    |--------------------------------------------------------------------------
    | Maximum attempts
    |--------------------------------------------------------------------------
    |
    | Maximum number of wrong OTP attempts before the OTP code is invalidated
    |
    */
    'max_attempts' => 3,
];

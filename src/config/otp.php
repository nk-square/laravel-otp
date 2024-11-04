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
    'max_attempts' => 5,
];

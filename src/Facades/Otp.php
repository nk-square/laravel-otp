<?php

namespace Nksquare\LaravelOtp\Facades;

use Illuminate\Support\Facades\Facade;
use Nksquare\LaravelOtp\Otp as OtpManager;

/**
 * Facade for the Otp provider
 */
class Otp extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return OtpManager::class;
    }
}
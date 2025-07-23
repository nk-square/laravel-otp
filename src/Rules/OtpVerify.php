<?php

namespace Nksquare\LaravelOtp\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Nksquare\LaravelOtp\Facades\Otp;

class OtpVerify implements ValidationRule
{
    protected array|string $recipients;

    public function __construct(array|string|null $recipients)
    {
        $this->recipients = $recipients==null ? [] : $recipients;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $attempts = Otp::getAttempts($this->recipients);
        
        if($attempts!==null && $attempts>=config('otp.max_attempts'))
        {
            $fail('Maximum attempts exceeded. Please generate a new OTP');
            return;
        }

        if(!Otp::verify($this->recipients,$value))
        {
            $fail(':attribute is invalid');
            Otp::increaseAttempts($this->recipients);
        }
    }

    public function invalidate() : void {
        Otp::forget($this->recipients);
    }
}

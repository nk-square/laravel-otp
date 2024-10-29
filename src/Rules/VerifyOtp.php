<?php

namespace Nksquare\LaravelOtp\Rules;

use Closure;
use Illuminate\Contracts\Validation\InvokableRule;
use Nksquare\LaravelOtp\Facades\Otp;

class VerifyOtp implements InvokableRule
{
    protected string|array $keys;

    protected bool $clearAfterSuccess = true;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string|array $keys,bool $clearAfterSuccess=true)
    {
        $this->keys = $keys;
        $this->clearAfterSuccess = $clearAfterSuccess;
    }

    public function __invoke($attribute,$value,$fail)
    {
        if(Otp::getAttempts($this->keys)>config('otp.max_attempts'))
        {
            Otp::clearOtp($this->keys);
            $fail(':attribute maximum attempts reached. Please generate a new OTP');
            return;
        }
        
        $verified = Otp::verify($this->keys,$value);

        Otp::increaseAttempts($this->keys);

        if(!$verified)
        {
            $fail(':attribute is invalid');
        }

        if($this->clearAfterSuccess && $verified)
        {
            Otp::clearOtp($this->keys);
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}

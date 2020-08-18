<?php

namespace Nksquare\LaravelOtp\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Nksquare\LaravelOtp\OtpMailable as Mailable;

class OtpMail extends Mailable
{
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('otp::otp-mail');
    }
}

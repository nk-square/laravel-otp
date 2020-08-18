<?php

namespace Nksquare\LaravelOtp\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class OtpMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var sting
     */
    public $code;
}

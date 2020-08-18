<?php

namespace Nksquare\LaravelOtp\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Mail\Mailer;
use Nksquare\LaravelOtp\Otp;
use Nksquare\LaravelOtp\Console\MailMakeCommand;

class OtpServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views','otp');

        $this->mergeConfigFrom(__DIR__ .'/../config/otp.php', 'otp');

        $this->publishes([
            __DIR__.'/../config/otp.php' => config_path('otp.php')
        ],'laravel-otp');

        Validator::extend('otp', function ($attribute, $value, $parameters, $validator) {
            $otp = app(Otp::class);
            return $otp->verify($parameters[0],$value);
        },'Invalid OTP');

        if ($this->app->runningInConsole()) 
        {
            $this->commands([
                MailMakeCommand::class
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Otp::class, function ($app) {
            $config = $app->config['otp'];
            $sms = $app->make($config['sms']);
            $mailer = $app->make(Mailer::class);
            $storage = $app->make($config['storage']);
            return new Otp($sms,$mailer,$storage);
        });
    }
}

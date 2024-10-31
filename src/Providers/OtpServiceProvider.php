<?php

namespace Nksquare\LaravelOtp\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Mail\Mailer;
use Nksquare\LaravelOtp\CodeGenerator;
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

        $this->publishes([
            __DIR__.'/../config/otp.php' => config_path('otp.php')
        ],'laravel-otp');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ .'/../config/otp.php', 'otp');
        
        $this->app->singleton(Otp::class, function ($app) {
            $config = $app->config['otp'];
            $storage = $app->make($config['storage']);
            $code = $app->make(CodeGenerator::class);
            return new Otp($storage,$code);
        });
    }
}

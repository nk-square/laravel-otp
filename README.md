# Laravel OTP
Laravel library for OTP verification via sms and email
## Installation
Run composer
```
composer require nksquare/laravel-otp
```
Publish config file
```
php artisan vendor:publish --provider="Nksquare\LaravelOtp\Providers\OtpServiceProvider" --tag="laravel-otp"
```
## Usage
Sending Otp
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Otp;

class OtpController extends Controller
{
    /**
     * sending otp
     */
    public function send()
    {
        Otp::sms('1234567890','Your otp for test is :code');
    }
}

```
Validating otp from user request
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Otp;

class OtpController extends Controller
{
    /**
     * sending otp
     */
    public function send()
    {
        Otp::sms('1234567890','Your otp for test is :code');
    }
    
    /**
     * validating otp
     */
    public function validateOtp(Request $request)
    {
        $this->validate($request,[
            'otp' => 'required|otp:1234567890',
        ]);
    }
}

```

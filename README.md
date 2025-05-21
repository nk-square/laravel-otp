# Laravel OTP
Laravel library for OTP verification via sms and email
## Dependencies
This library requires [nk-square/laravel-sms](https://github.com/nk-square/laravel-sms)
## Installation
Update your composer.json file
```
....
"repositories": [
    {
        "type": "git",
        "url": "https://github.com/nk-square/laravel-sms.git"
    },
    {
        "type": "git",
        "url": "https://github.com/nk-square/sms.git"
    },
    {
        "type": "git",
        "url": "https://github.com/nk-square/laravel-otp.git"
    }
....
```
Run composer
```
composer require nksquare/laravel-otp
```
Publish config file
```
php artisan vendor:publish --provider="Nksquare\LaravelOtp\Providers\OtpServiceProvider" --tag="laravel-otp"
```
## Usage
Please note that before sending otp you must have set up the [nk-square/laravel-sms](https://github.com/nk-square/laravel-sms) library for sms otp, and valid mail configurations in the .env file for email otp.\
Sending and validating the otp
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Nksquare\LaravelOtp\Rules\OtpVerify;
use Otp;

class OtpController extends Controller
{
    /**
     * sending otp
     */
    public function generateOtp()
    {
        // generate otp against a key
        $code = Otp::generate('9236852397');
        
        // send otp sms or email here
    }
    
    /**
     * validating otp
     */
    public function validateOtp(Request $request)
    {
        //validate the sms otp
        $this->validate($request,[
            'otp' => [new OtpVerify('9236852397')],
        ]);
    }
}

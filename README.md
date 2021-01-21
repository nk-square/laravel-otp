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
use Otp;

class OtpController extends Controller
{
    /**
     * sending otp
     */
    public function send()
    {
        //send sms otp
        Otp::sms('1234567890','Your otp for test is :code');
        
        //send email otp
        Otp::email('test@email.com');
    }
    
    /**
     * validating otp
     */
    public function validateOtp(Request $request)
    {
        //validate the sms otp
        $this->validate($request,[
            'otp' => 'required|otp:1234567890',
        ]);
        
        //validate the email otp
        $this->validate($request,[
            'otp_email' => 'required|otp:test@email.com',
        ]);
    }
}

```
## Customizing email
To use your own email instead of the default, run the command to generate an otp mail in the location app/Mail/MyOtp.php
```
php artisan otp:mail MyOtp --markdown=emails.my-otp
```
Inside the markdown file that you have created(resources/emails/my-otp.blade.php) you can simply access the otp code via the $code variable
```
@component('mail::message')
# OTP Verification
My Custom Otp Mail
Your OTP is {{$code}}. 
@endcomponent
```
And then finally instantiate your custom otp mail and pass it to the Otp::email method
```php
use App\Mail\MyOtp;
use Otp;

$myOtp = new MyOtp();
Otp::email('test@email.com',$myOtp);
```
## Same Otp to multiple channels
To send same otp to multiple channels use the following code
```php
use App\Mail\MyOtp;
use Otp;

$myOtp = new MyOtp();
Otp::send([
    'sms' => [
        'recipient' => '1234567890,
        'message' => 'Your otp for registration is :code'
    ],
    'email' => [
        'recipient' => 'test@email.com',
        'mailable' => $myOtp //optional
    ]
]);
```
To validate the otp pass the list of recipients as arguement, if any one of the recipients match the otp input the validation passes
```php
[
    'otp' => 'required|otp:1234567890,test@email.com'
];
```

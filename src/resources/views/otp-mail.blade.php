@component('mail::message')
# OTP Verification

Your OTP is {{$code}}. This OTP will expire in {{ceil(config('otp.ttl')/60)}} minutes.

Thanks,<br>
{{ config('app.name') }}
@endcomponent

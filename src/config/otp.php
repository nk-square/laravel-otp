<?php

return [
	'length' => 6,

	'ttl' => 600,

	'sms' => \Nksquare\LaravelOtp\Sms\Sms::class,

	'storage' => \Nksquare\LaravelOtp\Storage\SessionStorage::class,
];

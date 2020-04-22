<?php

namespace Nksquare\LaravelOtp\Storage;

interface StorageInterface 
{
	public function put($recipient,$code,$expire);

	public function get($recipient);

}
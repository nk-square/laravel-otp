<?php

namespace Nksquare\LaravelOtp;

class CodeGenerator
{
    public function generate($length) : string
    {
        return mt_rand('1'.str_repeat('0',$length-1),str_repeat('9',$length));
    }
}
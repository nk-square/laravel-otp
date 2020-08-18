<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcf3d25a8c42986300a218b36bae308f5
{
    public static $prefixLengthsPsr4 = array (
        'N' => 
        array (
            'Nksquare\\LaravelOtp\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Nksquare\\LaravelOtp\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcf3d25a8c42986300a218b36bae308f5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcf3d25a8c42986300a218b36bae308f5::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}

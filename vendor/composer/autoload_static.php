<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfc67e2d7741e8fc2de71cf1ca7d8d7de
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
        'B' => 
        array (
            'Bktechouse\\UrubutopayForWoocommerce\\' => 36,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
        'Bktechouse\\UrubutopayForWoocommerce\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfc67e2d7741e8fc2de71cf1ca7d8d7de::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfc67e2d7741e8fc2de71cf1ca7d8d7de::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitfc67e2d7741e8fc2de71cf1ca7d8d7de::$classMap;

        }, null, ClassLoader::class);
    }
}

<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitfc67e2d7741e8fc2de71cf1ca7d8d7de
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitfc67e2d7741e8fc2de71cf1ca7d8d7de', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitfc67e2d7741e8fc2de71cf1ca7d8d7de', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitfc67e2d7741e8fc2de71cf1ca7d8d7de::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}

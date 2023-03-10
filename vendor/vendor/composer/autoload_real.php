<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit74502e6248599f19ef5e4bf769a2fafb
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

        spl_autoload_register(array('ComposerAutoloaderInit74502e6248599f19ef5e4bf769a2fafb', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit74502e6248599f19ef5e4bf769a2fafb', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit74502e6248599f19ef5e4bf769a2fafb::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}

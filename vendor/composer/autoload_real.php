<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit1491a2a2afaa2f3e0b8b4637aabe151c
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

        spl_autoload_register(array('ComposerAutoloaderInit1491a2a2afaa2f3e0b8b4637aabe151c', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit1491a2a2afaa2f3e0b8b4637aabe151c', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit1491a2a2afaa2f3e0b8b4637aabe151c::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}

<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8fb5a962e5b1c3602633f297e8d60f47
{
    public static $prefixLengthsPsr4 = array (
        'N' => 
        array (
            'Nilambar\\Optioner\\' => 18,
        ),
        'K' => 
        array (
            'Kirki\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Nilambar\\Optioner\\' => 
        array (
            0 => __DIR__ . '/..' . '/ernilambar/optioner/src',
        ),
        'Kirki\\' => 
        array (
            0 => __DIR__ . '/..' . '/kirki-framework/url-getter/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8fb5a962e5b1c3602633f297e8d60f47::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8fb5a962e5b1c3602633f297e8d60f47::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit8fb5a962e5b1c3602633f297e8d60f47::$classMap;

        }, null, ClassLoader::class);
    }
}

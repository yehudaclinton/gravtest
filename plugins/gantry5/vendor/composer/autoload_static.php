<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdcd6bf3d92a7931c69b0f1529771ee9c
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'ScssPhp\\ScssPhp\\' => 16,
        ),
        'G' => 
        array (
            'Gantry\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ScssPhp\\ScssPhp\\' => 
        array (
            0 => __DIR__ . '/..' . '/scssphp/scssphp/src',
        ),
        'Gantry\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/classes/Gantry',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Gantry5\\Loader' => __DIR__ . '/../..' . '/src/Loader.php',
        'Gantry\\Debugger' => __DIR__ . '/../..' . '/Debugger.php',
        'Grav\\Plugin\\Gantry5Plugin' => __DIR__ . '/../..' . '/gantry5.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitdcd6bf3d92a7931c69b0f1529771ee9c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitdcd6bf3d92a7931c69b0f1529771ee9c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitdcd6bf3d92a7931c69b0f1529771ee9c::$classMap;

        }, null, ClassLoader::class);
    }
}
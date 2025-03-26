<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

/**
 * Legacy
 */
spl_autoload_register(function ($className) {
    if (false !== strpos($className, '\\')) {
        return false;
    }

    $parts = explode(' ', preg_replace('/[A-Z]/', ' $0', lcfirst($className)));
    $total = count($parts);

    // I look in the aplications
    if ($parts[$total - 1] == 'Model') {
        $file = SYSTEM_ABSPATH . 'app/' . $parts[0] . '/models/' . $className . '.php';
        if (is_file($file)) {
            include $file;
            return true;
        }

        $file = SYSTEM_ABSPATH . 'app/' . strtolower($parts[1]) . '/models/' . $parts[0] . '/' . $className . '.php';
        if (is_file($file)) {
            include $file;
            return true;
        }
    } else {
        // I look in the libraries
        $file = SYSTEM_ABSPATH . 'cms/libraries/' . $parts[0] . '/' . $className . '.php';
        if (is_file($file)) {
            include $file;
            return true;
        }

        // I look in the system library
        $file = SYSTEM_ABSPATH . 'cms/libraries/system/' . $className . '.php';
        if (is_file($file)) {
            include $file;
            return true;
        }
    }
});

/**
 * New framework autoload
 */
spl_autoload_register(function ($className) {
    if (strncmp($className, 'Junco\\', 6) !== 0) {
        return false;
    }

    $parts        = explode('\\', $className);
    $parts[0]    = 'cms/libraries';
    $parts[1]    = strtolower($parts[1]);
    $file        = SYSTEM_ABSPATH . implode('/', $parts) . '.php';

    if (is_file($file)) {
        include $file;
        return true;
    }

    return false;
});

/**
 * Psr's
 * 
 * With this I avoid using composer.
 */
spl_autoload_register(function ($className) {
    if (strncmp($className, 'Psr\\', 4) !== 0) {
        return false;
    }
    $replaces = [
        [
            'Psr\Log'            => 'log/src',                    // PSR-3
            'Psr\Http\Message'    => 'http-message/src',            // PSR-7
            'Psr\Container'        => 'container/src',                // PSR-11
            'Psr\Http\Server'    => 'http-server-handler/src',    // PSR-15
            'Psr\SimpleCache'    => 'simple-cache/src',            // PSR-16
            'Psr\Http\Client'    => 'http-client/src',            // PSR-18
        ],
        [
            'Psr\Http\Message'    => 'http-factory/src',            // PSR-17
            'Psr\Http\Server'    => 'http-server-middleware/src', // PSR-15
        ]
    ];

    foreach ($replaces as $replace) {
        $file = SYSTEM_ABSPATH . 'vendor/psr/' . strtr($className, $replace + ['\\' => '/']) . '.php';

        if (is_file($file)) {
            include $file;
            return true;
        }
    }

    return false;
});

if (is_file(SYSTEM_ABSPATH . '/vendor/autoload.php')) {
    require SYSTEM_ABSPATH . '/vendor/autoload.php';
}

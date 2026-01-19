<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class AutoloadHelper
{
    /**
     * Constructor
     */
    public function __construct() {}

    /**
     * Register
     */
    public function register(string ...$vendors): void
    {
        $files = [];

        foreach ($vendors as $vendor) {
            $files = glob(SYSTEM_ABSPATH . 'vendor/' . $vendor . '/*/composer.json') ?: [];
            foreach ($files as $file) {
                $composer = json_decode(file_get_contents($file), true);
                $basedir = dirname($file) . DIRECTORY_SEPARATOR;

                if (isset($composer['autoload']['psr-4'])) {
                    foreach ($composer['autoload']['psr-4'] as $namespace => $subPath) {
                        $this->registerNamespace($namespace, $basedir . $subPath);
                    }
                }
            }
        }
    }

    public function registerNamespace(string $namespace, string $basedir)
    {
        spl_autoload_register(function ($class) use ($namespace, $basedir) {
            if (0 !== strpos($class, $namespace)) {
                return;
            }

            $subClass = substr($class, strlen($namespace));
            $file = $basedir . str_replace('\\', '/', $subClass) . '.php';

            if (file_exists($file)) {
                require $file;
            }
        });
    }
}

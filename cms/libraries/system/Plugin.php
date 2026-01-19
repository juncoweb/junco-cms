<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

/**
 * Run a plugin function.
 *
 * @tutorial
 * 1- The name of a plugin identifies it uniquely for the entire framework.
 * 2- Within a plugin there can be many functions, each of them will be a different hook.
 * 3- An extension can have several alternatives for the same name of plugins.
 *    The plugins can be called as follows:
 *      - {extension_alias}
 *      - {extension_alias}.{alter_plugin}
 * 4- The path to the plugin will be composed as follows:
 *      - SYSTEM_ABSPATH . 'cms/plugins/{extension_alias}/{plugin_name}/{alter_plugin}/{plugin_name}.{plugin_hook}.php'
 */
class Plugin
{
    // vars
    protected $listener = null;

    /**
     * Constructor
     */
    private function __construct() {}

    /**
     * Find and leave ready the plugin to execute.
     * 
     * @param string $name     The name with which the plugin is recognized.
     * @param string $hook     Specific function within the plugin.
     * @param string $plugin   The plugin .
     * 
     * @return ?self           Plugin or null in case of not finding plugins
     */
    public static function get(string $name, string $hook, string $plugin): ?self
    {
        if ($plugin) {
            $plugin = explode('.', $plugin, 2);
            $file = SYSTEM_ABSPATH . 'cms/plugins/' . $plugin[0] . '/' . $name . '/' . ($plugin[1] ?? 'default') . '/' . $name . '.' . $hook . '.php';

            if (is_file($file)) {
                $self = new self();
                $self->listener = system_import($file);

                return $self;
            }
        }

        return null;
    }

    /**
     * Run the plugin.
     * 
     * @param mixed ...$args   The parameters that the plugin function requires.
     *
     * @return mixed           The return of the plugin function.
     */
    public function run(mixed ...$args): mixed
    {
        return call_user_func_array($this->listener, $args);
    }

    /**
     * Run the plugin.
     * 
     * @param mixed ...$args   The parameters that the plugin function requires.
     *
     * @return mixed           The return of the plugin function.
     */
    public function safeRun(mixed ...$args): mixed
    {
        try {
            return call_user_func_array($this->listener, $args);
        } catch (Throwable $e) {
            $this->log($e);
        }

        return null;
    }

    /**
     * Log
     */
    public function log(Throwable $e): void
    {
        app('logger')->alert(sprintf('%s: %s', get_class($e), $e->getMessage()), [
            'code'      => $e->getCode(),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
            'backtrace' => $e->getTraceAsString()
        ]);
    }
}

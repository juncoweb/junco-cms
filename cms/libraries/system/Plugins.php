<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

/**
 * Run a set of plugins.
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

class Plugins
{
	// vars
	protected $listeners = [];

	/**
	 * Constructor
	 */
	private function __construct() {}

	/**
	 * Find and leave ready the plugins to execute.
	 *
	 * @param string       $name     The name with which the plugin is recognized.
	 * @param string       $hook     Specific function within the plugin.
	 * @param string|array $plugins  Call to the plugin.
	 *
	 * @return    Object plugin or null in case of not finding plugins
	 */
	public static function get(string $name, string $hook, string|array $plugins)
	{
		if (!$plugins) {
			return;
		} elseif (!is_array($plugins)) {
			$plugins = explode(',', $plugins);
		}

		$self = new self();
		foreach ($plugins as $plugin_key) {
			$key = explode('.', $plugin_key, 2);
			$file = SYSTEM_ABSPATH . 'cms/plugins/' . $key[0] . '/' . $name . '/' . ($key[1] ?? 'default') . '/' . $name . '.' . $hook . '.php';

			if (is_file($file)) {
				$self->listeners[$plugin_key] = system_import($file);
			}
		}
		if ($self->listeners) {
			return $self;
		}
	}

	/**
	 * Run the plugins.
	 *
	 * @param mixed &$ref      Variable passed by reference to be modified.
	 * @param mixed ...$args   Other params.
	 */
	public function run(&$ref = null, ...$args)
	{
		$pass_key = is_object($ref) && method_exists($ref, 'setPluginKey');
		$args     = array_merge([&$ref], $args);

		foreach ($this->listeners as $key => $func) {
			if ($pass_key) {
				$ref->setPluginKey($key);
			}
			call_user_func_array($func, $args);
		}
	}
}

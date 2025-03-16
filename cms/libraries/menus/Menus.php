<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Menus
{
	// vars
	protected string $key;
	protected string $SEPARATOR	= '|';

	/**
	 * Get
	 * 
	 * @param string $snippet
	 * @param string $key
	 * 
	 * @return MenusInterface
	 */
	public static function get(string $snippet = '', string $key = ''): MenusInterface
	{
		return snippet('menus', $snippet, $key);
	}

	/**
	 * Constructor
	 * 
	 * @param string $key
	 */
	public function __construct(string $key = '')
	{
		$this->key = $key;
	}

	/**
	 * Read the data base
	 *
	 * To plugin a menu choose one of the following commands in the variable <menu_url>:
	 *  ?plugins(...)		// nested include
	 *  ?plugins(...):r		// replace command line for include
	 *  ?plugins(...):a		// add after the command line
	 * 
	 * @return array
	 */
	public function read(): array
	{
		if (!$this->key) {
			return [];
		}

		// query
		$rows = db()->safeFind("
		SELECT
		 id ,
		 menu_path ,
		 menu_order as ordering,
		 menu_url ,
		 menu_image ,
		 menu_hash ,
		 menu_params
		FROM `#__menus`
		WHERE menu_key = ?
		AND status = 1
		ORDER BY menu_path", $this->key)->fetchAll();

		return Nestedset::sortNestedArrays(
			Nestedset::toNestedArraysFromDepth(
				$this->parse($rows)
			)
		);
	}

	/**
	 * Parse
	 * 
	 * @param array $rows
	 * 
	 * @return array
	 */
	public function parse(array $rows): array
	{
		$menus = [];
		foreach ($rows as $row) {
			preg_match("@\?plugins\((.*)?\)(:.)?@", $row['menu_url'], $plugins);

			$path		= explode($this->SEPARATOR, $row['menu_path']);
			$modifier	= $plugins[2] ?? '';

			// Add the row
			if ($modifier != ':r') {
				if ($plugins) {
					$row['menu_url'] = str_replace($plugins[0], '', $row['menu_url']);
				}
				if (false !== strpos($row['menu_url'], ',')) {
					$row['menu_url'] = $this->getUrl($row['menu_url']);
				}

				$row['depth']		= count($path) - 1;
				$row['menu_name']	= _t($path[$row['depth']]);

				$menus[] = $row;
			}

			if ($plugins) {
				$collector = [];
				Plugins::get('menus', 'load', $plugins[1])?->run($collector);

				if ($collector) {
					if (in_array($modifier, [':r', ':a'])) {
						array_pop($path);
					}
					$basepath = implode($this->SEPARATOR, $path) . $this->SEPARATOR;
					$menu_key = $row['menu_key'] ?? $this->key;

					foreach ($collector as $row) {
						$row['menu_key']	= $menu_key;
						$row['menu_path']	= $basepath . $row['menu_path'];
						$path				= explode($this->SEPARATOR, $row['menu_path']);
						$row['depth']		= count($path) - 1;
						$row['menu_name']	= _t($path[$row['depth']]);

						$menus[] = $row;
					}
				}
			}
		}

		return $menus;
	}

	/**
	 * Get url
	 * 
	 * @param string $url
	 * 
	 * @return string
	 */
	protected function getUrl(string $url): string
	{
		$url  = explode(',', $url, 2);
		$args = [];

		if ($url[1]) {
			foreach (explode(',', $url[1]) as $var) {
				$var = explode('=', $var, 2);
				$args[$var[0]] = $var[1] ?? '';
			}
		}

		return url($url[0], $args);
	}
}

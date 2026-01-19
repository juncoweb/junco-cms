<?php

use Junco\Menus\PluginCollector;

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Menus
{
    // vars
    protected string $key;
    protected string $SEPARATOR = '|';

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
        $rows = db()->query("
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
            preg_match('/\?plugins\((.*)?\)(:.)?/', $row['menu_url'], $plugins);

            $path     = explode($this->SEPARATOR, $row['menu_path']);
            $modifier = $plugins[2] ?? '';

            // Add the row
            if ($modifier != ':r') {
                if ($plugins) {
                    $row['menu_url'] = str_replace($plugins[0], '', $row['menu_url']);
                }

                if (false !== strpos($row['menu_url'], ',')) {
                    $row['menu_url'] = $this->getUrl($row['menu_url']);
                }

                $row['depth']     = count($path) - 1;
                $row['menu_name'] = _t($path[$row['depth']]);

                $menus[] = $row;
            }

            // run the plugins
            if ($plugins) {
                $newRows = $this->runPlugins($plugins[1]);

                if ($newRows) {
                    if (in_array($modifier, [':r', ':a'])) {
                        array_pop($path);
                    }

                    $basepath = implode($this->SEPARATOR, $path) . $this->SEPARATOR;
                    $menu_key = $row['menu_key'] ?? $this->key;

                    foreach ($newRows as $row) {
                        $row['menu_key']  = $menu_key;
                        $row['menu_path'] = $basepath . $row['menu_path'];
                        $path             = explode($this->SEPARATOR, $row['menu_path']);
                        $row['depth']     = count($path) - 1;
                        $row['menu_name'] = _t($path[$row['depth']]);

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
        $partial = explode(',', $url, 2);
        $args = [];

        if ($partial[1]) {
            foreach (explode(',', $partial[1]) as $couple) {
                $couple = explode('=', $couple, 2);

                $args[$couple[0]] = $couple[1] ?? '';
            }
        }

        return url($partial[0], $args);
    }

    /**
     * Run
     * 
     * @param string $plugins
     * 
     * @return array
     */
    protected function runPlugins(string $plugins): array
    {
        $collector = new PluginCollector;

        Plugins::get('menus', 'load', $plugins)?->run($collector);

        return $collector->fetchAll();
    }
}

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Snippets
{
    // vars
    protected array  $defaults;
    protected string $index;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->defaults = config('system.default_snippets');
        $this->index    = router()->getAccessPoint();
    }

    /**
     * Gets an instance of a snippet.
     *
     * @param string   $name
     * @param ?string  $snippet   The particular alternative between all possible snippets.
     * @param mixed    ...$args   Any other argument.
     *
     * @return object  An instance of the object.
     */
    public function new(string $name, ?string $snippet = null, ...$args): ?object
    {
        $name = explode('#', $name);
        $extension = $name[0];
        $alterName = $name[1] ?? 'snippet';

        if ($snippet && $snippet !== 'default') {
            $snippet = explode('.', $snippet, 2);

            if (empty($snippet[1])) {
                $snippet[1] = 'default';
            }
        } else {
            $snippet = $this->defaults[$extension][$this->index] ?? ['master', 'default'];
        }

        if (!$snippet[0] || $snippet[0] == 'master') {
            $path  = $extension . '/master/' . $snippet[1];
            $class = $extension . '_master_' . $snippet[1] . '_' . $alterName;
        } else {
            $path  = $snippet[0] . '/' . $extension . '/' . $snippet[1];
            $class = $extension . '_' . $snippet[0] . '_' . $snippet[1] . '_' . $alterName;
        }

        $file = SYSTEM_ABSPATH . 'cms/snippets/' . $path . '/' . $alterName . '.php';

        /* if (!is_file($file)) {
			return null;
		} */

        include_once $file;
        return new $class(...$args);
    }
}

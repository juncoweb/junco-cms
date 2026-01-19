<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Extensions\Compiler;

class PluginCollector
{
    // vars
    protected $rows            = [];
    protected $plugins        = null;
    protected $plugin_key    = '';

    /**
     * Constructor
     */
    public function __construct(array $plugins)
    {
        $this->plugins = $plugins;
    }

    /**
     * Set Plugin key
     */
    public function setPluginKey(string $plugin_key): void
    {
        $this->plugin_key = $plugin_key;
    }

    /**
     * Set
     */
    public function set(
        string $caption,
        bool   $value = false,
        string $help = ''
    ): void {
        $this->rows[$this->plugin_key] = [
            'caption' => $caption,
            'value' => $value,
            'help' => $help,
        ];
    }

    /**
     * Get
     * 
     * @return array
     */
    public function get(): array
    {
        \Plugins::get('compiler', 'data', $this->plugins)?->run($this);
        return $this->rows;
    }
}

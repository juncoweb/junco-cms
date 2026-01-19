<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Settings;

final class PluginLoader
{
    //
    private int $total;

    /**
     * Constructor
     * 
     * @param array $rows
     */
    public function __construct(private array $rows)
    {
        $this->total = count($rows);
    }

    /**
     * Get
     * 
     * @return mixed
     */
    public function getValue(string $name): mixed
    {
        return $this->rows[$name]['value'] ?? null;
    }

    /**
     * Value
     * 
     * @return void
     */
    public function setValue(string $name, callable $callback, bool $alsoDefault = false): void
    {
        $this->rows[$name]['value'] = call_user_func($callback, $this->rows[$name]['value'] ?? '');

        if ($alsoDefault) {
            $this->rows[$name]['default_value'] = call_user_func($callback, $this->rows[$name]['default_value'] ?? '');
        }
    }

    /**
     * Help
     * 
     * @return void
     */
    public function setHelp(string $name, string $message = '', string ...$values): void
    {
        if (!$message) {
            $message = _t($this->rows[$name]['help'] ?? '?');
        }

        if ($values) {
            $message = vsprintf($message, $values);
        }

        $this->rows[$name]['alter_help'] = $message;
    }

    /**
     * Options
     * 
     * @return void
     */
    public function setOptions(string $name, array $options): void
    {
        $this->rows[$name]['options'] = $options;
    }

    /**
     * Snippet
     * 
     * @return void
     */
    public function setSnippet(string $name, string $snippet): void
    {
        $this->rows[$name]['snippet'] = $snippet;
    }

    /**
     * Plugin
     * 
     * @return void
     */
    public function setPlugin(string $name, string $plugin): void
    {
        $this->rows[$name]['plugin'] = $plugin;
    }

    /**
     * Plugins
     * 
     * @return void
     */
    public function setPlugins(string $name, string $plugin): void
    {
        $this->rows[$name]['plugins'] = $plugin;
    }

    /**
     * Ok
     * 
     * @return bool
     */
    public function ok(): bool
    {
        return $this->total == count($this->rows);
    }

    /**
     * Fetch all
     * 
     * @return array $rows
     */
    public function fetchAll(): array
    {
        return $this->rows;
    }
}

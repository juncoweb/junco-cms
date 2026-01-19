<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Settings;

final class PluginUpdater
{
    /**
     * Constructor
     * 
     * @param array $rows
     */
    public function __construct(private array $rows) {}

    /**
     * Get
     * 
     * @return mixed
     */
    public function getValue(string $name): mixed
    {
        return $this->rows[$name] ?? null;
    }

    /**
     * Value
     * 
     * @param string   $name
     * @param callable $callback
     * @param mixed    $default
     * 
     * @return void
     */
    public function setValue(string $name, callable $callback, mixed $default = null): void
    {
        $value = $this->rows[$name] ?? null;

        if ($value) {
            $value = call_user_func($callback, $value);
        }

        $this->rows[$name] = $value ?: $default;
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

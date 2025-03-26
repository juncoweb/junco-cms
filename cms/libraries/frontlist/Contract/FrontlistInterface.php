<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Frontlist\Contract;

use Pagination;

interface FrontlistInterface
{
    /**
     * Filters
     * 
     * @param string $snippet
     * 
     * @return FiltersInterface
     */
    public function getFilters(string $snippet = ''): FiltersInterface;

    /**
     * Sets the internal options.
     * 
     * @param array $options
     * 
     * @return void
     */
    public function setOptions(array $options): void;

    /**
     * Get the option or default value.
     * 
     * @param string $name
     * @param mixed  $default
     * 
     * @return mixed
     */

    public function getOption(string $name, mixed $default = null): mixed;
    /**
     * Load own assets.
     * 
     * @param bool $status
     * 
     * @return void
     */
    public function setAssets(bool $status = true): void;

    /**
     * Row
     * 
     * @param array $row
     */
    public function row(array $row): void;

    /**
     * Render
     * 
     * @param string $pagi
     * 
     * @return string
     */
    public function render(string $pagi = ''): string;
}

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Frontlist;

use Junco\Frontlist\Contract\FiltersInterface;
use Junco\Frontlist\Contract\FrontlistInterface;

abstract class FrontlistBase implements FrontlistInterface
{
    // vars
    protected FiltersInterface $filters;
    protected bool   $assets = false;
    protected string $filters_snippet = '';
    private   array  $options = [];
    //
    protected array $rows = [];
    protected array $row = [
        'id'          => '',
        'url'         => '',
        'image'       => '',
        'image_html'  => '',
        'title'       => '',
        'description' => '',
        'footer'      => '',
        'date'        => '',
        'author'      => '',
        'price'       => null,
        'button'      => '',
        'rating'      => '',
        'labels'      => [],
    ];
    public string $empty_list = '';

    /**
     * Filters
     * 
     * @param string $snippet
     * 
     * @return FiltersInterface
     */
    public function getFilters(string $snippet = ''): FiltersInterface
    {
        return $this->filters = snippet('frontlist#filters', $snippet ?: $this->filters_snippet);
    }

    /**
     * Sets the internal options.
     * 
     * @param array $options
     * 
     * @return void
     */
    public function setOptions(array $options): void
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Get the option or default value.
     * 
     * @param string $name
     * @param mixed  $default
     * 
     * @return mixed
     */
    public function getOption(string $name, mixed $default = null): mixed
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * Load own assets.
     * 
     * @param bool $status
     * 
     * @return void
     */
    public function setAssets(bool $status = true): void
    {
        $this->assets = $status;
    }

    /**
     * Assets
     * 
     * @param array $options
     * 
     * @return void
     */
    protected function assets(array $options): void
    {
        if ($this->assets && $options) {
            app('assets')->options($options);
        };
    }

    /**
     * Row
     * 
     * @param array $row
     */
    public function row(array $row): void
    {
        $this->rows[] = array_merge($this->row, $row);
    }
}

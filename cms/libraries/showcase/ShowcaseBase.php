<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Showcase;

use Junco\Showcase\Contract\ShowcaseInterface;
use Junco\Tabs\TabsInterface;

abstract class ShowcaseBase implements ShowcaseInterface
{
    // vars
    protected ?TabsInterface $tabs    = null;
    protected string $tabs_snippet    = '';
    protected array  $data            = [];
    //
    private   array  $options        = [];
    private   bool   $assets        = false;

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
     * Main
     * 
     * @param array $data
     */
    public function main(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Tabs
     * 
     * @param string $snippet
     * @param string $id
     * @param array  $options
     * 
     * @return TabsInterface
     */
    public function getTabs(string $snippet = '', string $id = '', array $options = []): TabsInterface
    {
        if (empty($options['class'])) {
            $options['class'] = 'responsive';
        } else {
            $options['class'] .= ' responsive';
        }

        return $this->tabs = snippet('tabs', $snippet ?: $this->tabs_snippet, $id, $options);
    }
}

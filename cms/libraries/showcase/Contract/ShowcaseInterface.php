<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Showcase\Contract;

use Junco\Tabs\TabsInterface;

interface ShowcaseInterface
{
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
     * Main
     * 
     * @param array $data
     */
    public function main(array $data): void;

    /**
     * Tabs
     * 
     * @param string $snippet
     * @param string $id
     * @param array  $options
     * 
     * @return TabsInterface
     */
    public function getTabs(string $snippet = '', string $id = '', array $options = []): TabsInterface;

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string;
}

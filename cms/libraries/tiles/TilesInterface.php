<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

interface TilesInterface
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
     * Look in a menu key
     * 
     * @param string $menu_key
     * 
     * @return void
     */
    public function fromMenuKey(string $menu_key): void;

    /**
     * Line
     * 
     * @param array  $tiles
     * 
     * @return void
     */
    public function line(array $tiles): void;

    /**
     * Separate
     * 
     * @param ?string $legend
     * 
     * @return void
     */
    public function separate(?string $legend = null): void;

    /**
     * Render
     */
    public function render(): string;

    /**
     * To string
     */
    public function __toString(): string;
}

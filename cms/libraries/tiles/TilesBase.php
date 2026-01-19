<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

abstract class TilesBase implements TilesInterface
{
    // vars
    protected array $lines   = [];
    protected array $blocks  = [];
    protected array $options = [];
    protected array $default_tile = [
        'id'      => '',
        'href'    => 'javascript:void(0)',
        'icon'    => 'fa-solid fa-file',
        'desc'    => '',
        'caption' => 'caption',
        'badge'   => null,
    ];

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
     * Look in a menu key
     * 
     * @param string $menu_key
     * 
     * @return void
     */
    public function fromMenuKey(string $menu_key): void
    {
        $rows = (new Menus($menu_key))->read();
        $tiles = [];

        foreach ($rows as $row) {
            $tiles[] = [
                'id'      => $row['menu_hash'],
                'href'    => $row['menu_url'],
                'icon'    => $row['menu_image'],
                'desc'    => '',
                'caption' => $row['menu_name'],
            ];
        }

        $this->line($tiles);
    }

    /**
     * Line
     * 
     * @param array  $tiles
     * 
     * @return void
     */
    public function line(array $tiles): void
    {
        foreach ($tiles as $i => $tile) {
            $tiles[$i] = array_merge($this->default_tile, $tile);
        }

        $this->lines[] = $tiles;
    }

    /**
     * Separate
     * 
     * @param string $legend
     * 
     * @return void
     */
    public function separate(?string $legend = null): void
    {
        if ($this->lines) {
            $this->blocks[] = [
                'legend' => $legend,
                'lines' => $this->lines
            ];

            $this->lines = [];
        }
    }

    /**
     * Separate
     * 
     * @param string $legend
     * 
     * @return void
     */
    public function __toString(): string
    {
        return $this->render();
    }
}

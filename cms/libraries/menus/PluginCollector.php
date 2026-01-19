<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Menus;

final class PluginCollector
{
    private array $rows = [];

    /**
     * Add
     * 
     * @param string $path
     * @param int    $ordering
     * @param string $url
     * @param string $image
     * @param string $hash
     * @param string $params
     * 
     * @return void
     */
    public function addRow(
        string $path,
        int    $ordering = 0,
        string $url = '',
        string $image = '',
        string $hash = '',
        string $params = ''
    ): void {
        $this->rows[] = [
            'menu_path'   => $path,
            'ordering'    => $ordering,
            'menu_url'    => $url,
            'menu_image'  => $image,
            'menu_hash'   => $hash,
            'menu_params' => $params,
        ];
    }

    /**
     * Fetch
     * 
     * @return array
     */
    public function fetchAll(): array
    {
        return $this->rows;
    }
}

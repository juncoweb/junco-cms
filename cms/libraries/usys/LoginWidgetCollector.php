<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Usys;

class LoginWidgetCollector
{
    // vars
    protected array $widgets = [];

    /**
     * Add new widget
     * 
     * @param string $url
     * @param string $caption
     * @param string $icon
     * @param string $bg_color
     * @param string $color
     */
    public function add(
        string $url,
        string $caption,
        string $icon = '',
        string $bg_color = '',
        string $color = ''
    ): void {
        $this->widgets[] = [
            'url'        => $url,
            'caption'    => $caption,
            'icon'        => $icon,
            'bg-color'    => $bg_color,
            'color'        => $color,
        ];
    }

    /**
     * Get all widgets
     */
    public function getAll(string|array $plugins): array
    {
        \Plugins::get('login', 'load', $plugins)?->run($this);

        return $this->widgets;
    }
}

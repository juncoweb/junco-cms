<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Form\FilterElements;
use Junco\Frontlist\Contract\FiltersInterface;

class frontlist_master_default_filters extends FilterElements implements FiltersInterface
{
    // vars
    protected $url = '';

    /**
     * Url
     * 
     * @param string $url
     * 
     * @return void
     */
    public function url(string $route = ''): void
    {
        $this->url = router()->getUrlForm($route);
    }

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string
    {
        $html = '';
        foreach ($this->rows as $content) {
            $html .= '<div class="btn-group">' . $content . '</div>';
        }

        return '<div id="list-filters" class="frontlist-filters">'
            . '<form action="' . $this->url['action'] . '">'
            . $this->url['hidden']
            . $html
            . $this->hidden
            . '</form></div>';
    }
}

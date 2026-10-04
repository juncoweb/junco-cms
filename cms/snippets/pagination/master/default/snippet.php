<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class pagination_master_default_snippet
{
    /**
     * Render
     * 
     * @param Pagination $pagi
     * 
     * @return string
     */
    public function render(Pagination $pagi): string
    {
        if ($pagi->num_pages < 2) {
            return '';
        }

        $data = $pagi->build([
            '<a href="' . $pagi->nav_href . '" title="{{ title }}" control-page="{{ page }}" class="btn">{{ placeholder }}</a>',
            '<span title="{{ title }}" class="btn disabled">{{ placeholder }}</span>',
            '<span title="{{ title }}" class="btn btn-primary btn-solid" aria-current="page">{{ placeholder }}</span>'
        ], [
            'prev'  => '&laquo;',
            'next'  => '&raquo;',
            'first' => '&laquo;&laquo;',
            'last'  => '&raquo;&raquo;'
        ], 1);

        return '<nav class="gl-pagination" aria-label="' . _t('Pagination') . '">'
            .  '<div class="btn-group">' . $data['first'] . $data['prev'] . '</div>'
            .  '<div class="btn-group">' . $data['numeration'] . '</div>'
            .  '<div class="btn-group">' . $data['next'] . $data['last'] . '</div>'
            . '</nav>';
    }
}

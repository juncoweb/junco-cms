<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class pagination_backlist_simple_snippet
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
            'prev' => '<i class="fa-solid fa-angle-left"></i>',
            'next' => '<i class="fa-solid fa-angle-right"></i>',
        ], 1, '');

        return '<nav class="backlist-pagination" aria-label="' . _t('Pagination') . '">'
            .  '<div class="btn-group">'
            .   $data['prev']
            .   $data['first_number']
            .   $data['numeration']
            .   $data['last_number']
            .   $data['next']
            .  '</div>'
            . '</nav>';
    }
}

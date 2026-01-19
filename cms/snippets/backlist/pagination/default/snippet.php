<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class pagination_backlist_default_snippet
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

        $pagi->nav_active = 'btn-primary btn-solid';
        $data = $pagi->build([
            '<a href="' . $pagi->nav_href . '" control-page="{{page}}" class="btn">{{placeholder}}</a>',
            '<span class="btn {{style}}">{{placeholder}}</span>'
        ], [
            'prev'    => '<i class="fa-solid fa-angle-left"></i>',
            'next'    => '<i class="fa-solid fa-angle-right"></i>',
            'first'    => '<i class="fa-solid fa-angles-left"></i>',
            'last'    => '<i class="fa-solid fa-angles-right"></i>'
        ], 2);

        return '<div class="backlist-pagination">'
            .  '<div class="btn-group">' . $data['first'] . $data['prev'] . '</div>'
            .  '<div class="btn-group">' . $data['numeration'] . '</div>'
            .  '<div class="btn-group">' . $data['next'] . $data['last'] . '</div>'
            .  '<span class="info">' . ($pagi->offset + 1) . ' - ' . ($pagi->cur_page == $pagi->num_pages ? $pagi->num_rows : $pagi->offset + $pagi->rows_per_page) . ' / ' . $pagi->num_rows . '</span>'
            . '</div>';
    }
}

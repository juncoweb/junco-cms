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

        $pagi->nav_active = 'btn-primary btn-solid';
        $data = $pagi->build(
            [
                '<a href="' . $pagi->nav_href . '" control-page="{{page}}" class="btn">{{placeholder}}</a>',
                '<span class="btn {{style}}">{{placeholder}}</span>'
            ],
            [
                'prev'    => '&laquo;',
                'next'    => '&raquo;',
                'first'    => '&laquo;&laquo;',
                'last'    => '&raquo;&raquo;'
            ],
            2
        );

        return '<div class="gl-pagination">'
            .  '<div class="btn-group">' . $data['first'] . $data['prev'] . '</div>'
            .  '<div class="btn-group">' . $data['numeration'] . '</div>'
            .  '<div class="btn-group">' . $data['next'] . $data['last'] . '</div>'
            . '</div>';
    }
}

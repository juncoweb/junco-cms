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
        $pagi->nav_active = 'btn-primary btn-solid';
        $data = $pagi->build(
            [
                '<a href="' . $pagi->nav_href . '" control-page="{{page}}" class="btn">{{placeholder}}</a>',
                '<span class="btn {{style}}">{{placeholder}}</span>'
            ],
            [
                'prev'    => '<i class="fa-solid fa-angle-left"></i>',
                'next'    => '<i class="fa-solid fa-angle-right"></i>',
            ],
            1,
            ''
        );

        return '<div class="backlist-pagination">'
            .  '<div class="btn-group">'
            .   $data['prev']
            .   $data['first_number']
            .   $data['numeration']
            .   $data['last_number']
            .   $data['next']
            .  '</div>'
            . '</div>';
    }
}

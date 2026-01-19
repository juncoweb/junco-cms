<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class pagination_backlist_short_snippet
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

        $data = $pagi->build(
            [
                '<a href="' . $pagi->nav_href . '" control-page="{{page}}" class="btn">{{placeholder}}</a>',
                '<span class="btn {{style}}">{{placeholder}}</span>'
            ],
            [
                'prev'    => '<i class="fa-solid fa-angle-left"></i>',
                'next'    => '<i class="fa-solid fa-angle-right"></i>'
            ]
        );

        return '<div class="backlist-pagination">'
            .  '<div class="btn-group">' . $data['prev'] . $data['next'] . '</div>'
            . '</div>';
    }
}

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class pagination_frontlist_comments_snippet
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
                '<a href="' . $pagi->nav_href . '" control-page="{{page}}">{{placeholder}}</a>',
                '<span class="{{style}}">{{placeholder}}</span>'
            ],
            [
                'prev'    => '<i class="fa-solid fa-angle-left"></i>',
                'next'    => '<i class="fa-solid fa-angle-right"></i>',
                'first'    => '<i class="fa-solid fa-angles-left"></i>',
                'last'    => '<i class="fa-solid fa-angles-right"></i>'
            ],
            2
        );

        return '<div class="fl-comments-pagination">'
            . '<div class="float-right">' . $data['first'] . $data['prev'] . $data['next'] . $data['last'] . '</div>'
            .  _t('Pages') . ': ' . $data['numeration']
            . '</div>';
    }
}

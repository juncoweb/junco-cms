<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function ($sections) {
    $html   = '';
    $plain  = '';
    $online = '';
    $CRLF    = "\r\n";
    $color  = config('email.snippet_color');

    foreach ($sections as $section) {
        $html  .= '<tr><td style="color: #333333; font-family: Arial, sans-serif; font-size: 20px; padding: 15px 0px 15px 0px;">' . $section['title'] . '</td></tr>';
        $plain .= '-- ' . $section['title'] . $CRLF . $CRLF;
        $online .= '<h2 class="nl-section">' . $section['title'] . '</h2>';
        $count  = 0;

        foreach ($section['articles'] as $article) {
            $table = '<table border="0" cellpadding="0" cellspacing="0" width="100%">'
                .    '<tr><td style="color: #333333; font-family: Arial, sans-serif; font-size: 18px; padding: 0px 0px 5px 0px;">'
                .    ($article['url']
                    ? '<a href="' . $article['url'] . '" style="color: ' . $color . '; text-decoration: none;"><font color="' . $color . '">' . $article['title'] . '</font></a>'
                    : $article['title'])
                .    '</td></tr>'
                .    ($article['meta'] ? '<tr><td style="color: #bbbbbb; font-family: Arial, sans-serif; font-size: 14px; padding: 0px 0px 5px 0px;">' . $article['meta'] . '</td></tr>' : '')
                .    ($article['content'] ? '<tr><td style="color: #333333; font-family: Arial, sans-serif; font-size: 14px; padding: 0px 0px 5px 0px;">' . $article['content'] . '</td></tr>' : '')
                .  '</table>';

            $_online = '<h3 class="nl-title">' . ($article['url'] ? '<a href="' . $article['url'] . '">' . $article['title'] . '</a>' : $article['title']) . '</h3>'
                . ($article['meta'] ? '<div class="nl-meta">' . $article['meta'] . '</div>' : '')
                . ($article['content'] ? '<div class="nl-content">' . $article['content'] . '</div>' : '');

            if ($article['image']) {
                $table = '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr valign="top" style="vertical-align:top">'
                    .   '<td width="80px" style="padding: 0px 15px 0px 0px;"><img src="' . $article['image'] . '"/></td>'
                    .   '<td>' . $table . '</td>'
                    . '</tr></table>';

                $_online = '<div class="nl-image"><div><img src="' . $article['image'] . '" title="' . $article['title'] . '"/></div><div>' . $_online . '</div></div>';
            }

            $html .= '<tr><td>' . $table . '</td></tr>'
                . '<tr><td>&nbsp;</td></tr>';

            $plain .= (++$count) . '.- ' . $article['title'] . $CRLF
                . ($article['meta'] ? $article['meta'] . ' - ' : '') . $article['content'] . $CRLF . $CRLF;

            $online .= $_online;
        }
    }

    return ['<table border="0" cellpadding="0" cellspacing="0" width="100%">' . $html . '</table>', strip_tags($plain), $online];
};

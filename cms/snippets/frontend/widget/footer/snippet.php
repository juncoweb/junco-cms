<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Template\WidgetBase;

class widget_frontend_footer_snippet extends WidgetBase
{
    /**
     * Render
     */
    function render(): string
    {
        $html  = '';
        foreach ($this->rows as $row) {
            $html .= "\n\t\t"
                . '<div class="widget' . ($row['css'] ? ' ' . $row['css'] : '') . '">'
                .  ($row['title'] ? '<h3 class="title">' . $row['title'] . '</h3>' : '')
                .   $row['content']
                . '</div>';
        }

        return '<div class="grid grid-4 grid-responsive">' . $html . "\n\t" . '</div>';
    }
}

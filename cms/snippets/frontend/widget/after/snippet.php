<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Template\WidgetBase;

class widget_frontend_after_snippet extends WidgetBase
{
    /**
     * Render
     * 
     * @return string
     */
    public  function render(): string
    {
        $html = '';
        foreach ($this->rows as $row) {
            $html .= "\n\t\t\t"
                . '<div class="widget' . ($row['css'] ? ' ' . $row['css'] : '') . '">'
                .  ($row['title'] ? '<h3 class="title">' . $row['title'] . '</h3>' : '')
                .   $row['content']
                . '</div>';
        }

        return $html;
    }
}

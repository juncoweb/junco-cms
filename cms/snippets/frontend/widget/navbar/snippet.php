<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Template\WidgetBase;

class widget_frontend_navbar_snippet extends WidgetBase
{
    /**
     * Render
     */
    public function render(): string
    {
        $html  = '';

        foreach ($this->rows as $row) {
            $html .= '<div' . ($row['css'] ? '  class="' . $row['css'] . '"' : '') . '>'
                . $row['content']
                . '</div>';
        }

        return $html;
    }
}

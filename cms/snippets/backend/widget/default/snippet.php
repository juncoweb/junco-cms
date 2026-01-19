<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Template\WidgetBase;

class widget_backend_default_snippet extends WidgetBase
{
    /**
     * Render
     * 
     * @return string
     */
    public function render(): string
    {
        $html = '';
        foreach ($this->rows as $row) {
            $html .= '<div' . ($row['css'] ? '  class="' . $row['css'] . '"' : '') . '>'
                .  $row['content']
                . '</div>';
        }

        return $html;
    }
}

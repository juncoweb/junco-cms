<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Template\WidgetBase;

class widget_frontend_bottom_snippet extends WidgetBase
{
    // vars
    protected $section = [
        'title'     => '',
        'content'   => '',
        'css'       => '',
        'container' => true
    ];

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string
    {
        $html = '';
        foreach ($this->rows as $row) {
            if ($row['title']) {
                $row['title'] = '<h3 class="title">' . $row['title'] . '</h3>';
            }
            if ($row['container'] === true) {
                $row['container'] = 'container';
            }

            $html .= "\n\t" . '<section class="widget' . ($row['css'] ? ' ' . $row['css'] : '') . '">'
                . '<div' . ($row['container'] ? ' class="' . $row['container'] . '"' : '') . '>'
                .   $row['title']
                .   $row['content']
                . '</div>'
                . '</section>';
        }

        return $html;
    }
};

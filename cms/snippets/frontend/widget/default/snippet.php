<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class widget_frontend_default_snippet
{
    // vars
    protected $rows = [];

    /**
     * Section
     * 
     * @param array $section
     */
    public function section(array $section)
    {
        $this->rows[] = array_merge([
            'title'        => '',
            'content'    => '',
            'css'        => ''
        ], $section);
    }

    /**
     * Render
     */
    public  function render()
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

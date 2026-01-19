<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Portal\PortalBase;

class portal_master_default_snippet extends PortalBase
{
    // vars
    protected array $section = [
        'title'     => '',
        'caption'   => '',
        'content'   => '',
        'css'       => '',
        'attr'      => [],
        'container' => true
    ];

    /**
     * Render
     */
    public function render(): string
    {
        $html = '';

        foreach ($this->rows as $row) {
            $header = '';
            $attr = [
                'class' => 'portal-section'
            ];

            if ($row['css']) {
                $attr['class'] .= ' ' . $row['css'];
            }

            if ($row['attr'] && is_array($row['attr'])) {
                if (isset($row['class'])) {
                    if ($row['class']) {
                        $attr['class'] .= ' ' . $row['css'];
                    }
                    unset($row['class']);
                }
                $attr = array_merge($row['attr'], $attr);
            }

            if ($row['title']) {
                $header .= '<h2 class="portal-title">' . $row['title'] . '</h2>';
            }

            if ($row['caption']) {
                $header .= '<div class="portal-caption">' . $row['caption'] . '</div>';
            }

            if ($header) {
                $header = '<div class="portal-header">' . $header . '</div>';
            }

            if ($row['container'] === true) {
                $row['container'] = 'container';
            }

            $html .= "\n\t" . '<section' . $this->attr($attr) . '>'
                . '<div' . ($row['container'] ? ' class="' . $row['container'] . '"' : '') . '>'
                . $header
                . $row['content']
                . '</div>'
                . '</section>';
        }

        return $html;
    }

    /**
     * Attr
     */
    protected function attr(array $attr): string
    {
        $html  = '';
        foreach ($attr as $key => $value) {
            $html .=  ' ' . $key . '="' . $value . '"';
        }

        return $html;
    }
}

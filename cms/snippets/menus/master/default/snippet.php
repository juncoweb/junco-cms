<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class menus_master_default_snippet extends MenusBase
{
    /**
     * Build
     * 
     * @param array $rows
     * @param int   $i
     * 
     * @return string
     */
    protected function build(array $rows, int $i = 0): string
    {
        if (!$rows) {
            return '';
        }

        $html = '';
        $indenter = str_repeat($this->TAB, $i * 2 + 1);

        foreach ($rows as $row) {
            $edge = isset($row['edge'])
                ? $this->build($row['edge'], $i + 1)
                : false;

            if (
                !$i
                && $this->only_if_has_edge
                && !$edge
            ) {
                continue;
            }

            $html .= $indenter . $this->TAB;

            if (substr($row['menu_name'], 0, 1) === $this->HR) {
                $html .= '<li class="separator" role="separator"></li>';
            } else {
                $html .= '<li><a href="' . $row['menu_url'] . '">' . $row['menu_name'] . '</a>' . $edge . '</li>';
            }

            $html .= $this->EOL;
        }

        return $html
            ? $this->EOL . $indenter . '<ul>' . $this->EOL . $html . $indenter . '</ul>'
            : '';
    }
}

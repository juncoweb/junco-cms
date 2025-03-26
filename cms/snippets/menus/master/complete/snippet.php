<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class menus_master_complete_snippet extends MenusBase
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

            if (!$i && $this->only_if_has_edge && !$edge) {
                continue;
            }

            $html .= $indenter . $this->TAB;

            if (substr($row['menu_name'], 0, 1) == $this->HR) {
                $html .= '<li class="separator" role="separator"></li>';
            } else {
                if (!$i) {
                    $menu_name = '<i class="' . $row['menu_image'] . '"></i><span>' . $row['menu_name'] . '</span>';
                } else {
                    $menu_name = $row['menu_name'];
                }

                if (empty($row['menu_url'])) {
                    $row['menu_url'] = 'javascript:void(0)';
                    $role = 'button';
                } elseif ($row['menu_url'] == 'javascript:void(0)') {
                    $role = 'button';
                } else {
                    $role = 'link';
                }

                if (!empty($row['menu_hash'])) {
                    $row['menu_hash'] = ' data-hash="' . $row['menu_hash'] . '"';
                } else {
                    $row['menu_hash'] = '';
                }

                $html .= '<li><a href="' . $row['menu_url'] . '"' . $row['menu_hash'] . ' role="' . $role . '" aria-label="' . $row['menu_name'] . '">'
                    .  $menu_name
                    . '</a>' . $edge . '</li>';
            }
            $html .= $this->EOL;
        }

        return $html
            ? $this->EOL . $indenter . '<ul>' . $this->EOL . $html . $indenter . '</ul>'
            : '';
    }
}

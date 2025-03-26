<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\ActionElement;

class Dropdown extends ActionElement
{
    /**
     * Dropdown
     *
     * @param string|array $menu
     * @param array        $attr
     */
    public function __construct(string|array $menu = '', array $attr = [])
    {
        $label = $this->extract($attr, 'label');
        $caret = $this->extract($attr, 'caret');

        $this->html = '<div class="btn-group">'
            .  '<button' . $this->attr([
                'type'            => 'button',
                'class'            => 'btn ' . ($caret ? 'dropdown-toggle' : 'dropdown'),
                'control-felem' => 'dropdown',
                'data-tooltip'    => '',
            ], $attr) . '>'
            .    $label
            .  '</button>'
            .  '<div class="dropdown-menu" style="display: none;">' . $menu . '</div>'
            . '</div>';
    }
}

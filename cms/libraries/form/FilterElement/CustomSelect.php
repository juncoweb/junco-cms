<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FilterElement;

class CustomSelect extends FilterElement
{
    // vars
    protected string $html = '';

    /**
     * Constructor
     *
     * @param string  $name
     * @param array   $options
     * @param ?string $default
     */
    public function __construct(
        string  $name = '',
        array   $options = [],
        ?string $default = null
    ) {
        if (!$options) {
            $options = ['' => ''];
        }
        if (!isset($options[$default])) {
            $default = array_key_first($options);
        }

        $html = '<button type="button" control-felem="select" data-on-change="submit" class="btn dropdown-toggle">'
            .   $options[$default]
            . '</button>'
            .  '<div class="dropdown-menu" style="display: none;">'
            .   '<input type="hidden" name="' . $name . '" value="' . $default . '">'
            .   $this->renderMenu($options, $default)
            . '</div>';

        $this->html = '<div class="btn-group">' . $html . '</div>';
    }

    /**
     * Render
     * 
     * @param array $options
     * 
     * @return string
     */
    protected function renderMenu(array $options, string $default)
    {
        $html = '';
        foreach ($options as $value => $label) {
            $html .= '<li data-select-value="' . $value . '"' . ($value === $default ? ' class="selected"' : '') . '>'
                . '<a href="javascript:void(0)">' . $label . '</a>'
                . '</li>';
        }

        return '<ul>' .  $html . '</ul>';
    }
}

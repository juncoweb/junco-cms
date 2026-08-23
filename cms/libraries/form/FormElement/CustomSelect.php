<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FormElement;

class CustomSelect extends FormElement
{
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

        $html = '<button type="button" id="__' . $name . '" control-felem="select" class="input-field input-select">'
            .   $options[$default]
            . '</button>'
            .  '<div class="dropdown-menu" style="display: none;">'
            .   '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . $default . '">'
            .   $this->renderMenu($options, $default)
            . '</div>';

        $this->name = '__' . $name;
        $this->content = '<div class="select-group">' . $html . '</div>';
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

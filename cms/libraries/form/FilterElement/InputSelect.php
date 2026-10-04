<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FilterElement;

class InputSelect extends FilterElement
{
    // vars
    protected string $html = '';

    /**
     * Constructor
     *
     * @param ?string $input_name
     * @param mixed   $input_value
     * @param string  $select_name
     * @param array   $options
     * @param ?string $default
     * 
     */
    public function __construct(
        ?string $input_name = null,
        mixed   $input_value = null,
        string  $select_name = '',
        array   $options = [],
        ?string $default = null
    ) {
        if (!isset($options[$default])) {
            $default = array_key_first($options);
        }

        $id = $select_name . '-menu';
        $html = '<div class="btn-group" control-felem="select" role="group" aria-label="' . _t('Search by field') . '">'
            .  '<input type="text" name="' . $input_name . '" value="' . $input_value . '" aria-label="' . _t('Text') . '" class="btn">'
            .  '<button type="submit" class="btn" data-select-label>' . $options[$default] . '</button>'
            .  '<button type="button" aria-label="' . ($t = _t('Expand menu')) . '" aria-expanded="false" aria-haspopup="listbox" aria-controls="' . $id . '" class="btn btn-caret">'
            //.     '<span class="visually-hidden">' . $t . '</span>'
            .  '</button>'
            .  '<div class="dropdown-menu" style="display: none;">'
            .    '<input type="hidden" name="' . $select_name . '" value="' . $default . '">'
            .    $this->renderMenu($options, $default, $id)
            .  '</div>'
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
    protected function renderMenu(array $options, string $default, string $id): string
    {
        $html = '';
        foreach ($options as $value => $label) {
            $html .= '<li data-select-value="' . $value . '"' . ($value === $default ? ' class="selected"' : '') . '>'
                . '<a href="javascript:void(0)"><i></i>' . $label . '</a>'
                . '</li>';
        }

        return '<ul id="' . $id . '">' .  $html . '</ul>';
    }
}

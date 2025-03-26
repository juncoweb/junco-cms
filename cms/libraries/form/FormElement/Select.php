<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FormElement;

class Select extends FormElement
{
    /**
     * Constructor
     *
     * @param string $name
     * @param string $default
     * @param array	 $options
     * @param array	 $attr
     */
    public function __construct(
        protected string $name,
        string $default = '',
        array  $options = [],
        array  $attr = [],
    ) {
        $html = '';

        foreach ($options as $value => $caption) {
            if (is_array($caption)) {
                $html .= '<optgroup label="' . $value . '">';

                foreach ($caption as $v => $c) {
                    $html .= '<option value="' . $v . '"' . ($v == $default ? ' selected="selected"' : '') . '>' . $c . '</option>';
                }

                $html .= '</optgroup>';
            } else {
                $html .= '<option value="' . $value . '"' . ($value == $default ? ' selected="selected"' : '') . '>' . $caption . '</option>';
            }
        }

        $this->html = '<select' . $this->attr([
            'name' => $name,
            'id' => $name,
            'class' => 'input-field'
        ], $attr) . '>' . $html . '</select>';
    }
}

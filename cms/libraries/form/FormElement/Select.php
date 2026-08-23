<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
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
        string|array $default = '',
        array  $options = [],
        array  $attr = [],
    ) {
        $multiple = $this->extract($attr, 'multiple', null) !== null;

        if ($multiple) {
            if (!is_array($default)) {
                $default = [$default];
            }
            $this->content = $this->renderMultiple($name, $default, $options, $attr);
            return;
        }
        $html = '';
        $class = 'input-field';

        if ($this->extract($attr, 'custom')) {
            $html .= '<button><selectedcontent></selectedcontent></button>';
            $class .= ' custom-select';
        }


        foreach ($options as $value => $label) {
            if (is_array($label)) {
                $html .= '<optgroup label="' . $value . '">';

                foreach ($label as $v => $c) {
                    $html .= '<option value="' . $v . '"' . ($v == $default ? ' selected="selected"' : '') . '>' . $c . '</option>';
                }

                $html .= '</optgroup>';
            } else {
                $html .= '<option value="' . $value . '"' . ($value == $default ? ' selected="selected"' : '') . '>' . $label . '</option>';
            }
        }

        $this->content = '<select' . $this->attr([
            'name'  => $name,
            'id'    => $name,
            'class' => $class
        ], $attr) . '>' . $html . '</select>';
    }

    public function renderMultiple(
        string $name,
        array  $default = [],
        array  $options = [],
        array  $attr = [],
    ) {
        $html = '';
        foreach ($options as $value => $label) {
            $html .= '<option value="' . $value . '"' . (in_array($value, $default) ? ' selected="selected"' : '') . '>' . $label . '</option>';
        }

        return '<select' . $this->attr([
            'name'     => $name . '[]',
            'id'       => $name,
            'multiple' => '',
            'class'    => 'input-field'
        ], $attr) . '>' . $html . '</select>';
    }
}

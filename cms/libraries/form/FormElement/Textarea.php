<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FormElement;

class Textarea extends FormElement
{
    /**
     * Constructor
     *
     * @param string $name
     * @param string $value
     * @param array	 $attr
     */
    public function __construct(
        protected string $name,
        string $value,
        array  $attr = []
    ) {
        if ($value) {
            $value = str_replace('<br />', '', $value);
        }

        $max_chars = $this->extract($attr, 'max-chars');
        if ($max_chars) {
            $attr['control-felem']    = 'max-chars';
            $attr['data-max-chars'] = $max_chars;
        }

        if (isset($attr['auto-grow'])) {
            $attr['rows'] ??= 1;
            $attr['control-felem'] = (isset($attr['control-felem']) ? $attr['control-felem'] . ' ' : '') . 'auto-grow';
        }

        $this->content = '<textarea' . $this->attr([
            'name'        => $name,
            'id'          => $name,
            'rows'        => 8,
            'placeholder' => _t('Write') . '...',
            'class'       => 'input-field',
        ], $attr) . '>' . $value . '</textarea>';
    }
}

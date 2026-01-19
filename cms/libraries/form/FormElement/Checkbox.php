<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FormElement;

class Checkbox extends FormElement
{
    /**
     * Constructor
     * 
     * @param string $name
     * @param mixed  $default
     * @param array  $attr
     */
    public function __construct(
        protected string $name,
        mixed $default = null,
        array $attr = []
    ) {

        $html = '<input' . $this->attr([
            'type'  => 'checkbox',
            'name'  => $name,
            'value' => 1,
            'class' => 'input-checkbox'
        ], $attr) . (empty($default) ? '' : ' checked') . '/>';

        $this->content  = $html;
    }

    /**
     * Set
     * 
     * @param ?string $label
     * 
     * @return self
     */
    public function setLabel(?string $label = ''): self
    {
        if ($label) {
            $this->content = '<label class="input-label">' . $this->content . '<span class="ml-2">' . $label . '</span></label>';
        } else {
            $this->label = $label;
        }

        return $this;
    }
}

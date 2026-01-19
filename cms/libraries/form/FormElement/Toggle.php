<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FormElement;

class Toggle extends FormElement
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
        $this->content = '<input' . $this->attr([
            'type'  => 'checkbox',
            'name'  => $name,
            'id'    => $name,
            'value' => 1,
            'class' => 'input-toggle'
        ], $attr) . ($value ? ' checked' : '') . '/>';
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

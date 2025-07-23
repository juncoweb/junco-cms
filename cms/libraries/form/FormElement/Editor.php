<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FormElement;

class Editor extends FormElement
{
    /**
     * Constructor
     *
     * @param string  $name
     * @param string  $value
     */
    public function __construct(
        protected string $name,
        string $value = ''
    ) {
        if ($value) {
            $value = htmlentities($value);
        }

        $this->content = '<textarea name="' . $name . '" control-felem="editor" class="input-field">' . $value . '</textarea>';
    }
}

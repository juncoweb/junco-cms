<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FormElement;

use Junco\Form\Contract\HiddenInterface;

class Hidden implements HiddenInterface
{
    //
    protected string $content = '';

    /**
     * Constructor
     */
    public function __construct(string $name, mixed $value = '')
    {
        if (!$value) {
            return;
        }

        if (is_array($value)) {
            $html = '';
            foreach ($value as $key => $v) {
                $html .= '<input type="hidden" name="' . $name . '[' . $key . ']" value="' . $v . '"/>';
            }

            $this->content = $html;
        } else {
            $this->content = '<input type="hidden" name="' . $name . '" value="' . $value . '"/>';
        }
    }

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string
    {
        return $this->content;
    }

    /**
     * To string representation.
     * 
     * @return string
     */
    public function __toString(): string
    {
        return $this->content;
    }
}

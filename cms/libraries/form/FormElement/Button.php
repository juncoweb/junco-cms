<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FormElement;

class Button extends FormElement
{
    /**
     * Constructor
     * 
     * @param array $attr
     */
    public function __construct(array $attr = [])
    {
        $label = '';

        if (isset($attr['icon'])) {
            $label .= '<i class="' . $attr['icon'] . '" aria-hidden="true"></i>';
            unset($attr['icon']);
        }

        if (isset($attr['label'])) {
            if (empty($attr['title'])) {
                $attr['title'] = $attr['label'];
            }

            if ($label) {
                $label .= ' ';
            }

            $label .= $attr['label'];
            unset($attr['label']);
        }

        if (isset($attr['title'])) {
            $label .= '<span class="visually-hidden">' . $attr['title'] . '</span>';
        }

        if (isset($attr['name'])) {
            $attr['type'] ??= 'button';
        }

        $tagName = isset($attr['href'])
            ? 'a'
            : (isset($attr['type']) ? 'button' : 'div');

        $this->html = '<' . $tagName . $this->attr(['class' => 'btn'], $attr) . '>' . $label . '</' . $tagName . '>';
    }
}

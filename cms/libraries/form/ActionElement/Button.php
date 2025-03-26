<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\ActionElement;

class Button extends ActionElement
{
    /**
     * Button
     *
     * @param array	$attr
     */
    public function __construct(array $attr = [])
    {
        $label = $this->extract($attr, 'label', 'Button');
        $tagName = isset($attr['href']) ? 'a'
            : (isset($attr['type']) ? 'button' : 'div');

        $this->html = '<' . $tagName . $this->attr([
            'class' => 'btn',
            'data-tooltip' => ''
        ], $attr) . '>' . $label . '</' . $tagName . '>';
    }
}

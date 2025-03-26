<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\ActionElement;

use Junco\Form\Contract\ActionElementInterface;

abstract class ActionElement implements ActionElementInterface
{
    // vars
    protected string  $html    = '';
    protected string  $help    = '';

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string
    {
        return $this->html;
    }

    /**
     * To string representation.
     * 
     * @return string
     */
    public function __toString(): string
    {
        return $this->html;
    }

    /**
     * Merge attributes
     */
    protected function attr(array $a, array $b)
    {
        if ($b) {
            if (isset($b['class'])) {
                $a['class'] .= ' ' . $b['class'];
                unset($b['class']);
            }

            $a = array_merge($a, $b);
        }

        $html  = '';
        foreach ($a as $n => $v) {
            $html .=  ' ' . $n . '="' . $v . '"';
        }

        return $html;
    }

    /**
     * Extract attributes
     */
    protected function extract(array &$attr, string $name, $value = '')
    {
        if (isset($attr[$name])) {
            $value = $attr[$name];
            unset($attr[$name]);
        }

        return $value;
    }

    /**
     * Get
     *
     * @param array	&$attr
     */
    protected function getLabel(array &$attr = []): string
    {
        $label        = $this->extract($attr, 'label', '{{ icon }}{{ caption }}');
        $icon        = $this->extract($attr, 'icon');
        $caption    = $this->extract($attr, 'caption');

        if ($icon) {
            $icon = '<i class="' . $icon . '" aria-hidden="true"></i>' . ($label ? ' ' : '');
        }

        return strtr($label, [
            '{{ icon }}' => $icon,
            '{{ caption }}' => $caption,
            '{{ title }}' => $attr['title'],
        ]);
    }
}

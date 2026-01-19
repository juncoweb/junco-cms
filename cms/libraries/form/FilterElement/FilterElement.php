<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FilterElement;

use Junco\Form\Contract\FilterElementInterface;

abstract class FilterElement implements FilterElementInterface
{
    // vars
    protected string $html = '';

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
}

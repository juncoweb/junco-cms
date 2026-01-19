<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Column;

use Junco\Backlist\Contract\ColumnBaseInterface;

class ColumnBase implements ColumnBaseInterface
{
    protected string $th        = '';
    protected string $th_class  = '';
    protected string $td        = '';
    protected string $td_after  = '';
    protected string $td_before = '';
    protected string $td_class  = '';
    protected string $width     = '';
    protected string $keep      = '';

    /**
     * Head
     * 
     * @return string
     */
    public function th(): string
    {
        $attr = '';

        if ($this->width) {
            $attr .= ' width="' . (is_numeric($this->width) ? $this->width .= 'px' : $this->width) . '"';
        }

        if ($this->th_class) {
            $attr .= ' class="' . trim($this->th_class) . '"';
        }

        return '<th' . $attr . '>' . $this->th . '</th>';
    }

    /**
     * Body
     * 
     * @return string
     */
    public function td(): string
    {
        $attr = '';

        if ($this->td_class) {
            $attr .= ' class="' . trim($this->td_class) . '"';
        }

        return '<td' . $attr . '>'
            . ($this->keep ? '<!-- keep={{ ' . $this->keep . ' }} -->' : '')
            . $this->td_before . $this->td . $this->td_after
            . '</td>';
    }

    /**
     * Normalize
     * 
     * @param string $input
     * 
     * @return string
     */
    protected function normalize(string $input): string
    {
        if ($input[0] != ':') {
            return $input;
        }

        return '{{ ' . substr($input, 1) . ' }}';
    }

    /**
     * Attr
     * 
     * @param array $attr
     * 
     * @return string
     */
    protected function attr(array $attr): string
    {
        $output = '';
        foreach ($attr as $k => $v) {
            $output .=  ' ' . $k . '="' . $v . '"';
        }

        return $output;
    }
}

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Column;

use Junco\Backlist\Contract\ButtonInterface;

class Button extends Column implements ButtonInterface
{
    use IconTextTrait;
    use ControlTrait;

    /**
     * Constructor
     * 
     * @param string $control
     */
    public function __construct(string $control = '')
    {
        if ($control) {
            $this->setControl($control);
        }
    }

    /**
     * Body
     * 
     * @return string
     */
    public function td(): string
    {
        $this->attr['title'] ??= 'Untitled';
        $this->attr['class'] ??= 'btn-inline';

        $caption = $this->getCaption($this->text, $this->icon, $this->attr['title']);

        if (isset($this->attr['control-list'])) {
            if ($this->text) {
                $this->td = '<a' . $this->attr(array_merge(['href' => 'javascript:void(0)', 'role' => 'button'], $this->attr)) . '>' . $caption . '</a>';
            } else {
                $this->td = '<button' . $this->attr(array_merge(['type' => 'button'], $this->attr)) . '>' . $caption . '</button>';
            }
        } else {
            $this->td = '<div' . $this->attr($this->attr) . '>' . $caption . '</div>';
        }

        return parent::td();
    }
}

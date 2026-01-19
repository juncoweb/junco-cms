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
        $this->td = isset($this->attr['control-list'])
            ? '<a' . $this->attr($this->attr) . '>' . $caption . '</a>'
            : '<div' . $this->attr($this->attr) . '>' . $caption . '</div>';

        return parent::td();
    }
}

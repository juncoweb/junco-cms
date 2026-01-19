<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Column;

use Junco\Backlist\Contract\ControlInterface;

class Control extends Column implements ControlInterface
{
    use IconTextTrait;
    use ControlTrait;

    /**
     * Constructor
     * 
     * @param string $control
     */
    public function __construct(string $control)
    {
        $this->setControl($control);
    }

    /**
     * Set
     * 
     * @param string $icon
     * 
     * @return string
     */
    public function td(): string
    {
        $this->attr['title'] ??= _t('Show');
        $this->attr['class'] ??= 'table-linked';

        $caption = $this->getCaption($this->text, $this->icon, $this->attr['title']);

        $this->td = '<a' . $this->attr($this->attr) . '>' . $caption . '</a>';

        return parent::td();
    }
}

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Column;

trait ControlTrait
{
    /**
     * Set
     * 
     * @param string $control
     */
    protected function setControl(string $control = ''): static
    {
        $this->attr['href']         = 'javascript:void(0)';
        $this->attr['control-list'] = $this->normalize($control);

        return $this;
    }
}

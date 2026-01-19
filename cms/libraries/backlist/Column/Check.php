<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Column;

use Junco\Backlist\Contract\CheckInterface;

class Check extends ColumnBase implements CheckInterface
{
    /**
     * Constructor
     * 
     * @param string $value
     * @param string $index
     */
    public function __construct(string $value, string $index = '')
    {
        $value = $value
            ? $this->normalize($value)
            : '{{ id }}';

        $index = $index
            ? $this->normalize($index)
            : 'id';


        $this->th = '<input type="checkbox" control-row="check-all" aria-label="' . _t('Select all') . '" class="input-checkbox"/>';
        $this->td = '<input'
            .  ' type="checkbox"'
            .  ' name="' . $index . '[]"'
            .  ' value="' . $value . '"'
            .  ' title="ID ' . $value . '"'
            .  ' aria-label="' . _t('Select row') . '"'
            .  ' class="input-checkbox"'
            . '/>';
        $this->width = 20;
    }
}

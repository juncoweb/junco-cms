<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FilterElement;

class Checkbox extends FilterElement
{
    /**
     * Constructor
     *
     * @param string $name
     * @param bool	 $checked
     * @param array  $attr
     */
    public function __construct(string $name = '', bool $checked = false, array $attr = [])
    {
        if (!empty($attr['arrow'])) {
            $attr['icon'] = $checked ? 'fa-solid fa-arrow-up' : 'fa-solid fa-arrow-down';
            $attr['hidden'] = true;
        }

        if (!empty($attr['icon'])) {
            $attr['label'] = '<i class="' . $attr['icon'] . '" title="' . ($attr['label'] ?? '') . '"></i>';
        }

        $this->html = '<label class="btn">'
            . '<input'
            .  ' type="checkbox" name="' . $name . '"'
            .  ' value="1"'
            .  ' control-felem="submit"'
            .  ' data-value="change"'
            .  ' class="input-checkbox mr-2' . (empty($attr['hidden']) ? '' : ' input-hidden') . '"'
            .   ($checked ? ' checked' : '')
            . ' />'
            . '<span>' . ($attr['label'] ?? '?') . '</span>'
            . '</label>';
    }
}

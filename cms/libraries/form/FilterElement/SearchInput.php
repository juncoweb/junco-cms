<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\FilterElement;

class SearchInput extends FilterElement
{
    /**
     * Constructor
     *
     * @param string  $name
     * @param ?string $label
     * @param array	  $attr
     */
    public function __construct(
        protected string $name,
        mixed $value,
        array  $attr = []
    ) {
        $this->html = '<div class="btn-group">'
            . '<input type="text" name="' . $name . '" value="' . $value . '" aria-label="' . _t('Search') . '" class="btn"/>'
            //. ($clear_url && $value ? '<a href="'. $clear_url .'"><i class="fa-solid fa-xmark"></i></a>' : '')
            .  '<button type="submit" class="btn">'
            .     '<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><div class="visually-hidden">' . _t('Enter') . '</div>'
            .  '</button>'
            . '</div>';
    }
}

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Form\Enum\SelectColor;
use Junco\Form\FormElement\CustomSelect;

return function (array $attr): CustomSelect {
    $name    = $attr['name'] ?? '';
    $default = $attr['value'] ?? '';
    $options = SelectColor::getList();

    foreach ($options as $value => $label) {
        $options[$value] = '<span><i class="fa-solid fa-circle color-' . $value . '" aria-hidden="true"></i> ' . $label . '</span>';
    }

    return new CustomSelect($name, $options, $default);
};

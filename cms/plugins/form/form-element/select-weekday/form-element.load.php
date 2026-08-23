<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Form\FormElement\CustomElement;

return function (array $attr): CustomElement {
    $options = $attr['options'] ?? Date::getDays();
    $name    = $attr['name'] ?? '';
    $default = $attr['value'] ?? [];
    $counter = 0;
    $html    = '';

    foreach ($options as $value => $label) {
        $id = $name . '[' . $counter . ']';
        $checked = in_array($value, $default)
            ? ' checked'
            : '';

        $html .= '<label class="btn btn-pill btn-small rounded-full" title="' . $label . '">'
            . '<input type="checkbox" id="' . $id . '" name="' . $name . '[]" value="' . $value . '"' . $checked . ' class="input-hidden"/> '
            . '<span class="visually-hidden">' . $label . '</span>'
            . '<span aria-hidden="true">' . mb_substr($label, 0, 1) . '</span>'
            . '</label> ';

        $counter++;
    }

    return new CustomElement('', '<div id="' . $name . '" role="group">' . $html . '</div>');
};

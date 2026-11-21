<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Form\FormElement\CustomElement;

return function (array $attr): CustomElement {
    $resizes  = config('filesystem.image_resizes');
    $options  = [];
    $sizes    = [];

    if (!empty($attr['inherit'])) {
        $options[''] = '--- ' . _t('Inherit') . ' ---';
    }

    foreach ($resizes as $key => $resize) {
        $options[$key] = $resize['name'];
        $sizes = [
            ...array_keys($resize['sizes'])
        ];
    }
    $default = $attr['value']['key'] ?? '';
    $defaults = $attr['value']['sizes'] ?? $sizes;

    $html = '';
    foreach ($options as $value => $label) {
        $html .= '<option value="' . $value . '"' . ($value == $default ? ' selected="selected"' : '') . '>' . $label . '</option>';
    }

    $html = '<div class="input-group">'
        . '<div class="btn"><i class="fa-solid fa-crop-simple"></i></div>'
        . '<select name="' . $attr['name'] . '[key]" id="' . $attr['name'] . '" class="input-field">' . $html . '</select>'
        . '</div>';

    $html .= '<div class="mt-4">';
    foreach ($sizes as $size) {
        $html .= '<label class="input-label">'
            . '<input type="checkbox" name="' . $attr['name'] . '[sizes][]" value="' . $size . '"' . (in_array($size, $defaults) ? ' checked="checked"' : '') . ' class="input-checkbox"/> '
            . ucfirst($size)
            . '</label>';
    }
    $html .= '</div>';



    return new CustomElement($attr['name'], $html);
};

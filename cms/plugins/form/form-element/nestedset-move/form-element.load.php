<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Form\FormElement\CustomElement;

return function (array $attr): CustomElement {
    $options = $attr['options'] ?? Date::getDays();
    $name    = $attr['name'] ?? '';
    $html    = '';

    if ($options) {
        foreach ($options as $option) {
            $html .= '<tr>'
                . '<td>' . str_repeat('<span class="color-subtle">|— </span>', $option['depth'] - 1) . $option['label'] . '</td>'
                . '<td class="text-center"><input type="radio" name="' . $name . '" value="' . $option['value'] . '" class="input-radio" /></td>'
                . '<td class="text-center"><input type="radio" name="' . $name . '" value="-' . $option['value'] . '" class="input-radio" /></td>'
                . '</tr>';
        }

        $html = '<table class="table table-highlight table-striped">'
            . '<thead><tr>'
            .   '<th><label><input type="radio" name="' . $name . '" class="input-radio" checked /> ' . _t('At the beginning') . '</label></th>'
            .   '<th width="20%"><div class="text-center">' . _t('Here after') . '</div></th>'
            .   '<th width="20%"><div class="text-center">' . _t('First child') . '</div></th>'
            . '</tr></thead><tbody>' . $html . '</tbody>'
            . '</table>';
    }

    return new CustomElement('', '<div id="' . $name . '" role="group" class="table-responsive">' . $html . '</div>');
};

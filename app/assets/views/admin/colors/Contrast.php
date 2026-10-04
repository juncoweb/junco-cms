<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

function create_box(string $type, string $color, string $weight = ''): string
{
    //$text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
    //$text = 'Lorem ipsum dolor sit amet.';
    $text = '';
    $name = $type;
    if ($color != 'default') {
        $name .= "-$color";
    }

    if ($weight) {
        $name .= "-$weight";
    }

    return '<div color-contrast class="p-4" style="color: var(--' . $name . '-font-color); background: var(--' . $name . '-bg-color);">'
        . '<div class="p-4 mt-1" style="border: 1px solid var(--' . $name . '-border-color);">' . $text . '</div>'
        . '</div>';
}

function create_type(string $type, string $color): string
{
    return '<div class="flex mb-4">'
        . create_box($type, $color, 'disabled')
        . create_box($type, $color)
        . create_box($type, $color, 'active')
        . '</div>';
}

function create_types(string $color): string
{
    $html = '';
    foreach (['regular', 'solid'] as $type) {
        $html .= '<div>'
            . '<h2 class="text-uppercase">' . $type . ' / ' . $color . '</h2>'
            . '<div class="flex">' . create_type($type, $color) . '</div>'
            . '</div>';
    }

    return '<div class="flex gap-4 mb-4">' . $html . '</div>';
}

$colors = ['default', 'primary', 'secondary', 'info', 'warning', 'success', 'danger'];
$html = '';

foreach ($colors as $color) {
    $html .= create_types($color);
}

/* $html .= create_box('regular', [
    'subtle-font',
    'font',
    'heading',
]); */


// template
$tpl = Template::get();
$tpl->options([
    'css' => 'cms/scripts/system/css/test-colors.css',
    'js' => 'app/assets/js/admin.colors.js',
    'thirdbar' => 'assets.colors'
]);
$tpl->title(_t('Contrast'), 'fa-solid fa-circle-half-stroke');
$tpl->content($html);

return $tpl->response();

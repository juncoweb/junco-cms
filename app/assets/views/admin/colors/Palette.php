<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

function create_theme(string $theme, string $html): string
{
    return '<div data-theme="' . $theme . '" class="mb-4">'
        . '<div id="theme-surface-' . $theme . '" class="p-4" style="background: var(--theme-surface-color);">'
        . '<div class="text-center text-uppercase bold">' . $theme . '</div>'
        . '<div class="flex">' . $html . '</div>'
        . '</div>'
        . '</div>';
}

function create_palette(array $colors, string $theme): string
{
    $values = ["50", "100", "200", "300", "400", "500", "600", "700", "800", "900", "950"];
    $html = '';

    //
    $html .= '<div><div class="pl-4 pr-4 bold">#</div>';
    foreach ($values as $value) {
        $html .= '<div style="background: white; color: black;" class="p-4">' . $value . '</div>';
    }
    $html .= '</div>';

    //
    foreach ($colors as $color) {
        $html .= '<div>'
            . '<div class="pl-4 pr-4 bold">' . $color . '</div>';
        foreach ($values as $value) {
            $html .= '<div style="background: var(--theme-' . $color . '-color-' . $value . ');" data-color class="p-4"></div>';
        }
        $html .= '</div>';
    }
    $html = '<div class="flex mb-4">' . $html . '</div>';

    //return create_theme($theme, $html);
    return $html;
}

$colors = ['default', 'primary', 'secondary', 'info', 'warning', 'success', 'danger'];
$html = create_palette($colors, 'light');
//$html .= create_palette($colors, 'dark');

// template
$tpl = Template::get();
$tpl->options([
    'css' => 'cms/scripts/system/css/test-colors.css',
    'js' => 'app/assets/js/admin.colors.js',
    'thirdbar' => 'assets.colors'
]);
$tpl->title(_t('Palette'), 'fa-solid fa-palette');
$tpl->content($html);

return $tpl->response();

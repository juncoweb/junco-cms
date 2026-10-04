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

function create_pallette(array $colors, string $theme): string
{
    $html = '';
    foreach ($colors as $color) {
        $html .= '<div>'
            . '<div class="pl-4 pr-4 bold">' . $color . '</div>'
            . '<div style="background: var(--theme-' . $color . '-color);" data-color="theme-surface-' . $theme . '" class="p-4"></div>'
            . '</div>';
    }

    return create_theme($theme, $html);
}

$colors = ['default', 'primary', 'secondary', 'info', 'warning', 'success', 'danger'];
$html = create_pallette($colors, 'light');
$html .= create_pallette($colors, 'dark') . '</div>';

// template
$tpl = Template::get();
$tpl->options([
    'css' => 'cms/scripts/system/css/test-colors.css',
    'js' => 'app/assets/js/admin.colors.js',
    'thirdbar' => 'assets.colors'
]);
$tpl->title(_t('Colors'), 'fa-solid fa-paintbrush');
$tpl->content($html);

return $tpl->response();

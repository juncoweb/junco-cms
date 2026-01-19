<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

/**
 * 
 */
function create_table(string $type): string
{
    $typeClass = '';
    $colorClass = '';
    if ($type !== 'regular') {
        $typeClass = ' box-' . $type;
        $colorClass = ' color-' . $type;
    }

    $colors = ['default', 'primary', 'secondary'];
    //$colors = array_merge($colors, ['info', 'success', 'warning', 'danger']);
    $tones = ['disabled', '', 'active'];
    $tr = '';

    foreach ($colors as $color) {
        $td = '';

        foreach ($tones as $tone) {
            $class = $color . ($tone ? '-' . $tone : '');
            $td .= '<td class="box-' . $class . $typeClass . '">'
                . '<div class="box-' . $class . $typeClass . '" style="border-width: 3px; border-style: solid; padding: 10px;">'
                //. '<h1>Lorem ipsum</h1>'
                . '<p>' . $class . $typeClass . '</p>'
                . '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>'
                . '<p class="color-subtle-default color-' . $color . $colorClass . '">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>'
                . '</div>'
                . '</td>';
        }

        $tr .= '<tr>' . $td . '</tr>';
    }

    return '<h2>' . ucfirst($type) . '</h2>'
        . '<table class="table">' . $tr . '</table>';
}

//
$html = create_table('regular');
$html .= create_table('solid');

// template
$tpl = Template::get();
$tpl->options([
    'css' => 'cms/scripts/system/css/test-colors.css',
    'thirdbar' => 'system.thirdbar'
]);
$tpl->title('Colors');
$tpl->content($html);

return $tpl->response();

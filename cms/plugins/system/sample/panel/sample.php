<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// vars
$colors = ['default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'];
$html = '';

// ---
$partial = '';
foreach ($colors as $color) {
    $partial .= '<div class="panel panel-' . $color . '">'
        .   '<div class="panel-header"><h4>' . ucfirst($color) . '</h4></div>'
        .   '<div class="panel-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa</div>'
        . '</div>';
}
$html .= '<h4>.panel</h4><div class="grid grid-medium-box mb-4">' . $partial . '</div>';

// ---
$partial = '';
foreach ($colors as $color) {
    $partial .= '<div class="panel panel-' . $color . ' panel-solid">'
        .   '<div class="panel-header"><h4>' . ucfirst($color) . '</h4></div>'
        .   '<div class="panel-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa</div>'
        . '</div>';
}
$html .= '<h4>.panel .panel-solid</h4><div class="grid grid-medium-box mb-4">' . $partial . '</div>';

// ---
$partial = '';
foreach ($colors as $color) {
    $partial .= '<div class="panel panel-' . $color . ' panel-regular">'
        .   '<div class="panel-header"><h4>' . ucfirst($color) . '</h4></div>'
        .   '<div class="panel-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa</div>'
        . '</div>';
}
$html .= '<h4>.panel .panel-regular</h4><div class="grid grid-medium-box mb-4">' . $partial . '</div>';

// template
$tpl = Template::get();
$tpl->options([
    'thirdbar' => 'system.thirdbar'
]);
$tpl->title('Panel');
$tpl->content = $html;

return $tpl->response();

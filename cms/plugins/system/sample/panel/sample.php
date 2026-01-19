<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// samples
$samples = Samples::get();

// 1
$samples
    ->html(implode(PHP_EOL, [
        '<div class="panel">',
        '<div class="panel-header"><h4>Example</h4></div>',
        '<div class="panel-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa</div>',
        '</div>'
    ]))
    ->setLabel('Example 1');

// 2
$samples
    ->colors('<div class="panel panel-{{ color }}">'
        .   '<div class="panel-header"><h4>{{ caption }}</h4></div>'
        .   '<div class="panel-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa</div>'
        . '</div>')
    ->setLabel('.panel');

// 3
$samples
    ->colors('<div class="panel panel-{{ color }} panel-solid">'
        .   '<div class="panel-header"><h4>{{ caption }}</h4></div>'
        .   '<div class="panel-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa</div>'
        . '</div>')
    ->setLabel('.panel .panel-solid');

// 4
$samples
    ->colors('<div class="panel panel-{{ color }} panel-regular">'
        .   '<div class="panel-header"><h4>{{ caption }}</h4></div>'
        .   '<div class="panel-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa</div>'
        . '</div>')
    ->setLabel('.panel .panel-regular');

$html = $samples->render();

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'system.thirdbar']);
$tpl->title('Panel');
$tpl->content($html);

return $tpl->response();

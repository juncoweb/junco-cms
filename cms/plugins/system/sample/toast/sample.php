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
    ->js("JsToast('Lorem ipsum')")
    ->setLabel('Basic Toast');

// 2
$samples
    ->js("JsToast({message: 'Lorem ipsum', type: 'warning'})")
    ->setLabel('With Type');

// 3
$samples
    ->colors('<div class="toast toast-{{ color }}">'
        .   '<div class="toast-body">{{ caption }}</div>'
        .   '<div class="toast-close"><i class="fa-solid fa-xmark"></i></div>'
        . '</div>')
    ->setLabel('Colors');

$html = $samples->render();

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'system.thirdbar']);
$tpl->title('Toast');
$tpl->content($html);

return $tpl->response();

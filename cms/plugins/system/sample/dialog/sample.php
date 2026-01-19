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
    ->html('<div class="dialog dialog-info">Lorem ipsum dolor sit amet consectetur adipiscing elit erat torquent vivamus feugiat.</div>')
    ->setLabel('Example 1');

// 2
$samples
    ->colors('<div class="dialog dialog-{{ color }}">{{ caption }}</div>')
    ->setLabel('Colors')
    ->setFull();

$html = $samples->render();

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'system.thirdbar']);
$tpl->title('Dialog');
$tpl->content($html);

return $tpl->response();

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
    ->colors('<div class="badge badge-{{ color }}">{{ caption }}</div>')
    ->setLabel('.badge')
    ->setInline();

// 2
$samples
    ->colors('<div class="badge badge-regular badge-{{ color }}">{{ caption }}</div>')
    ->setLabel('.badge .badge-regular')
    ->setInline();

// 3
$samples
    ->colors('<div class="badge badge-{{ color }} badge-small">{{ caption }}</div>')
    ->setLabel('.badge .badge-small')
    ->setInline();

// 4
$samples
    ->colors('<div class="badge badge-{{ color }} badge-large">{{ caption }}</div>')
    ->setLabel('.badge .badge-large')
    ->setInline();

$html = $samples->render();

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'system.thirdbar']);
$tpl->title('Badge');
$tpl->content($html);

return $tpl->response();

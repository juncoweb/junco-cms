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
    ->html('<button type="button" class="btn-inline">Button</button> | <a href="javascript:void(0)">Link</a>')
    ->setLabel('.btn-inline');

// 1
$samples
    ->html('<div class="panel panel-info panel-solid p-4">Text | <button type="button" class="btn-inline">Button</button> | <a href="javascript:void(0)">Link</a></div>')
    ->setLabel('.btn-inline');

$html = $samples->render();

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'form.thirdbar']);
$tpl->title('Button group');
$tpl->content($html);

return $tpl->response();

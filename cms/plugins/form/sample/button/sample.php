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
    ->html('<button class="btn">Button</button>')
    ->setLabel('Example 1');

$samples->separate();

// vars
$html = '<h2>Regular</h2>';

// 2
$samples
    ->colors('<button class="btn btn-{{ color }}">{{ caption }}</button>')
    ->setLabel('.btn')
    ->setInline();

// 3
$samples
    ->colors('<button class="btn btn-{{ color }} disabled">{{ caption }}</button>')
    ->setLabel('.btn .disabled')
    ->setInline();

// 4
$samples
    ->colors('<button class="btn btn-{{ color }} btn-outline">{{ caption }}</button>')
    ->setLabel('.btn .btn-outline')
    ->setInline();

$samples->separate('Regular');

// 5
$samples
    ->colors('<button class="btn btn-solid btn-{{ color }}">{{ caption }}</button>')
    ->setLabel('.btn .btn-solid')
    ->setInline();

// 6
$samples
    ->colors('<button class="btn btn-solid btn-{{ color }} disabled">{{ caption }}</button>')
    ->setLabel('.btn .btn-solid .disabled')
    ->setInline();

// 7
$samples
    ->colors('<button class="btn btn-solid btn-{{ color }} btn-outline">{{ caption }}</button>')
    ->setLabel('.btn .btn-solid .btn-outline')
    ->setInline();

$samples->separate('Solid');

$html = $samples->render();

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'form.thirdbar']);
$tpl->title('Button');
$tpl->content($html);

return $tpl->response();

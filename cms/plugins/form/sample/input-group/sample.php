<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// elements
$felem = Form::getElements();

// samples
$samples = Samples::get();
$samples
    ->html($felem->input('name'))
    ->setLabel('.input-field');

$samples
    ->html($felem->input('name', ['type' => 'file']))
    ->setLabel('.input-file');

$samples
    ->html($felem->input('name', ['control-felem' => 'color']))
    ->setLabel('control-felem="color"');

$samples
    ->html($felem->select('name', ['--- Default ---']))
    ->setLabel('.input-field[type=select]');

//
$html = $felem->group(
    $felem->input('name', ['placeholder' => 'Input']),
    $felem->checkbox('name')
);
$samples
    ->html($html)
    ->setLabel('.input-group');

//
$html = $felem->group(
    $felem->button(['icon' => 'fa-solid fa-user']),
    $felem->input('name'),
    $felem->button(['label' => '@'])
);
$samples
    ->html($html)
    ->setLabel('.input-group');

//
$html = $felem->group(
    $felem->button(['icon' => 'fa-solid fa-user']),
    $felem->input('name1', ['placeholder' => 'Input 1']),
    $felem->input('name2', ['placeholder' => 'Input 2']),
);
$samples
    ->html($html)
    ->setLabel('.input-group');

//
$html = $felem->group(
    $felem->button(['icon' => 'fa-solid fa-user']),
    $felem->textarea('name', ['placeholder' => 'Textarea', 'control-felem' => 'auto-grow', 'rows' => 1])
);
$samples->html($html)->setLabel('.input-group');

$html = $samples->render();

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'form.thirdbar']);
$tpl->title('Form group');
$tpl->content($html);

return $tpl->response();

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

// medium
$samples
    ->colors($felem->input('name', [
        'placeholder' => '{{ caption }}',
        'icon' => [
            'position' => 'right',
            'name' => 'fa-solid fa-search',
            'class' => 'input-{{ color }}',
        ]
    ]))
    ->setLabel('.input-icon-group');

// small
$samples
    ->colors($felem->input('name', [
        'placeholder' => '{{ caption }}',
        'icon' => [
            //'position' => 'right',
            'name' => 'fa-solid fa-search',
            'class' => 'input-small input-{{ color }}',
        ]
    ]))
    ->setLabel('.input-icon-group .input-small');

// large
$samples
    ->colors($felem->input('name', [
        'placeholder' => '{{ caption }}',
        'icon' => [
            'position' => 'right',
            'name' => 'fa-solid fa-search',
            'class' => 'input-large input-{{ color }}',
        ]
    ]))
    ->setLabel('.input-icon-group .input-large');

$html = $samples->render();

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'form.thirdbar']);
$tpl->title('Input');
$tpl->content($html);

return $tpl->response();

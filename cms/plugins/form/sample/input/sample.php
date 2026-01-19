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

// 1
$samples
    ->colors($felem->input('name', [
        'placeholder' => '{{ caption }}',
        'class' => 'input-{{ color }}'
    ]))
    ->setLabel('.input');

// 2
$element = $felem->group(
    $felem->button(['icon' => 'fa-solid fa-user']),
    $felem->input('name', ['placeholder' => '{{ caption }}']),
    $felem->input('name', ['placeholder' => 'Input 2'])
);
$samples
    ->colors('<div class="input-group input-{{ color }} btn-{{ color }}">' . $element . '</div>')
    ->setLabel('.input-group');

// 3
$element = $felem->group(
    $felem->button(['icon' => 'fa-solid fa-user']),
    $felem->input('name', ['placeholder' => '{{ caption }}']),
    //$felem->input('name', ['placeholder' => 'Input 2'])
);
$samples
    ->colors('<div class="input-group input-{{ color }} btn-{{ color }} btn-solid">' . $element . '</div>')
    ->setLabel('.input-group .btn-solid');

// 4
$samples
    ->colors($felem->textarea('name', [
        'placeholder' => '{{ caption }}',
        'class' => 'input-{{ color }}',
        'control-felem' => 'auto-grow'
    ]))
    ->setLabel('.input-field[type=textarea]');

// 5
$element = $felem->group(
    $felem->button(['icon' => 'fa-solid fa-user']),
    $felem->input('name', ['placeholder' => '{{ caption }}']),
    //$felem->input('name', ['placeholder' => 'Input 2'])
);
$samples
    ->colors('<div class="input-group input-{{ color }} input-small btn-{{ color }} btn-small">' . $element . '</div>')
    ->setLabel('.input-group .btn-small');

// 6
$element = $felem->group(
    $felem->button(['icon' => 'fa-solid fa-user']),
    $felem->input('name', ['placeholder' => '{{ caption }}']),
    //$felem->input('name', ['placeholder' => 'Input 2'])
);
$samples
    ->colors('<div class="input-group input-{{ color }} input-large btn-{{ color }} btn-large">' . $element . '</div>')
    ->setLabel('.input-group .btn-large');

// 7
$samples
    ->colors('<div class="panel panel-solid panel-{{ color }}" style="padding: 10px">'
        . '<div class="input-icon-group input-solid input-{{ color }}">'
        . $felem->input('name', [
            'placeholder' => '{{ caption }}',
            'class' => 'input-{{ color }}'
        ])
        . '</div></div>')
    ->setLabel('On a background colour');

$html = $samples->render(true);


// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'form.thirdbar']);
$tpl->title('Input');
$tpl->content($html);

return $tpl->response();

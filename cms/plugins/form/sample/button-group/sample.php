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
    ->html('<div class="btn-group">'
        . '<button class="btn">Button 1</button>'
        . '<button class="btn">Button 2</button>'
        . '</div>')
    ->setLabel('.btn-group');

// 2
$samples
    ->html('<div class="btn-group btn-large">'
        . '<button class="btn">Button 1</button>'
        . '<button class="btn">Button 2</button>'
        . '</div>')
    ->setLabel('.btn-large');

// 3
$samples
    ->html('<div class="btn-group btn-small">'
        . '<button class="btn">Button 1</button>'
        . '<button class="btn">Button 2</button>'
        . '</div>')
    ->setLabel('.btn-small');

// 4
$samples
    ->html('<div class="btn-group btn-small">'
        . '<a href="javascript:void(0)" class="btn">Button</a>'
        . '<div class="btn-group">'
        . '<button control-felem="select" class="btn dropdown-toggle">All classes</button>'
        . '<div class="dropdown-menu" style="display: none;"><input type="hidden" name="class" value="0"><ul><li data-select-value="0" class="selected"><a href="javascript:void(0)"><i></i>All classes</a></li><li data-select-value="1"><a href="javascript:void(0)"><i></i>Administrator</a></li><li data-select-value="15"><a href="javascript:void(0)"><i></i>Default</a></li></ul></div>'
        . '</div>'
        //. '<a href="javascript:void(0)" class="btn">Button</a>'
        . '</div>')
    ->setLabel('.btn-group');

// 
$samples
    ->html('<div class="btn-group" control-felem="select">'
        .     '<input class="btn"/>'
        .     '<button type="submit" class="btn" data-select-label>All classes</button>'
        .     '<button class="btn dropdown-toggle"></button>'
        .     '<div class="dropdown-menu" style="display: none;"><input type="hidden" name="class" value="0"><ul><li data-select-value="0" class="selected"><a href="javascript:void(0)"><i></i>All classes</a></li><li data-select-value="1"><a href="javascript:void(0)"><i></i>Administrator</a></li><li data-select-value="15"><a href="javascript:void(0)"><i></i>Default</a></li></ul></div>'
        .   '</div>')
    ->setLabel('.btn-group (2)');

// 
$samples
    ->html('<div class="btn-group"><label class="btn btn-press" control-felem="press">Button 1<input type="checkbox" /></label></div>')
    ->setLabel('.btn-press (simple)');

// 
$samples
    ->html('<div class="btn-group"><label class="btn btn-press" control-felem="press">Button 1<input type="radio" name="test" /></label><label class="btn btn-press" control-felem="press">Button 2<input type="radio" name="test" /></label></div>')
    ->setLabel('.btn-press (multiple)');

// 3
$samples
    ->html('<div class="btn-group rounded-full btn-small">'
        . '<button class="btn">Button 1</button>'
        . '<button class="btn">Button 2</button>'
        . '</div>')
    ->setLabel('.rounded-full');

$html = $samples->render();

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'form.thirdbar']);
$tpl->title('Button group');
$tpl->content($html);

return $tpl->response();

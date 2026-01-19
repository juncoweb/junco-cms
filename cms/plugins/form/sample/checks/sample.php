<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// vars
$felem = Form::getElements();
$samples = Samples::get();

// 1
$samples
    ->colors($felem->checkbox('checkbox', ['class' => 'input-{{ color }}'])->setLabel('{{ caption }}'))
    ->setLabel('.input-checkbox');

// 2
$samples
    ->colors($felem->toggle('toggle', ['class' => 'input-{{ color }}'])->setLabel('{{ caption }}'))
    ->setLabel('.input-toggle');

// 3
$samples
    ->colors($felem->radio('radio', ['' => '{{ caption }}'], ['class' => 'input-{{ color }}']))
    ->setLabel('.input-radio');

$html = $samples->render();

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'form.thirdbar']);
$tpl->title('Checks');
$tpl->content($html);

return $tpl->response();

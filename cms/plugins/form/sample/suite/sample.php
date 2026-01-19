<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;


$options = ['Opción 1', 'Opción 2', 'Opción 3', 'Opción 4', 'Opción 5', 'Opción 6'];
$values = [
    'suite_1' => [0, 1, 2, 3, 4, 5],
];


// form
$form = Form::get();
$form->setValues($values);
$form->suite('title', $options)->setLabel(_t('Title'));
$html = $form->render();

// template
$tpl = Template::get();
$tpl->options([
    'domready' => 'JsForm().request()',
    'thirdbar' => 'form.thirdbar'
]);
$tpl->title('Suite');
$tpl->content('<div class="panel"><div class="panel-body">' . $html . '</div></div>');

return $tpl->response();

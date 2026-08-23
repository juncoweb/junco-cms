<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Form\Enum\SelectColor;

defined('IS_TEST') or die;

// form
$form = Form::get();
$form->setValues([]);
$form->load('form.select-weekday')->setLabel('Weekday');
$html = $form->render();

// template
$tpl = Template::get();
$tpl->options([
    'domready' => 'JsForm().request()',
    'thirdbar' => 'form.thirdbar'
]);
$tpl->title('Select Weekday');
$tpl->content('<div class="panel"><div class="m-4">' . $html . '</div></div>');

return $tpl->response();

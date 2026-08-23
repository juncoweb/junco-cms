<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Form\Enum\SelectColor;

defined('IS_TEST') or die;


$options = SelectColor::getList();
foreach ($options as $value => $label) {
    $options[$value] = '<span class="color-' . $value . '" aria-hidden="true">&#x2B24;</span> '
        . '<span>' . $label . '</span>';
}


// form
$form = Form::get();
$form->setValues([]);
$form->select('real', $options, ['custom' => true])->setLabel('Custom native');
$form->select('real', $options)->setLabel('Native');
$form->load('form.select-color', ['name' => 'select-color'])->setLabel('Custom');
$html = $form->render();

// template
$tpl = Template::get();
$tpl->options([
    'domready' => 'JsForm().request()',
    'thirdbar' => 'form.thirdbar'
]);
$tpl->title('Select Color');
$tpl->content('<div class="panel"><div class="m-4">' . $html . '</div></div>');

return $tpl->response();

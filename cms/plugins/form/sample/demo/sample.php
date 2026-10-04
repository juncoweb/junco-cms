<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;


// form
$form = Form::get();
//
$form->columns(
    $form->input('name')->setLabel(_t('Name'))->setHelp('Name of the plugin.'),
    $form->input('tag')->setLabel('Tag')
);
$form->group(
    $form->input('name')->setLabel(_t('Name'))->setHelp('Name of the plugin.'),
    $form->toggle('type')
);

$form->separate();
//
$form->toggle('separate')
    ->setLabel(_t('Separate'))
    ->setHelp(_t('Separate by categories'));
$form->separate(_t('Settings'));

$html = '<div class="panel"><div class="panel-body">' . $form->render() . '</div></div>';

// template
$tpl = Template::get();
$tpl->options([
    'thirdbar' => 'form.thirdbar',
    'domready' => 'JsForm()'
]);
$tpl->title('Form Group');
$tpl->content($html);

return $tpl->response();

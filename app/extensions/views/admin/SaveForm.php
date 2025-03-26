<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

$form = Form::get();
$form->setValues($values);
$form->hidden('id');
//
$form->input('extension_alias')->setLabel(_t('Alias'))->setRequired();
$form->input('extension_name')->setLabel(_t('Name'));
$form->select('developer_id', $developers)->setLabel(_t('Developer'))->setRequired();
$form->textarea('extension_abstract', ['auto-grow' => ''])->setLabel(_t('Abstract'));
$form->separate();
//
$form->addRow(['help' => _t('If left empty, these values will be filled with the default values.')]);
$form->input('extension_credits')->setLabel(_t('Credits'));
$form->input('extension_license')->setLabel(_t('License'));
$form->separate(_t('Legal'));
//
$form->input('extension_require')->setLabel(_t('Since'))->setRequired();
if ($can_be_a_package) {
    $form->toggle('is_package')->setLabel(_t('Is package'));
} else {
    $form->addRow(['label' => _t('Annexed to'), 'content' => '<div class="badge badge-secondary">' . $annexed_to . '</div>']);
}

$form->separate(_t('Package'));

// modal
$modal = Modal::get();
if (!$is_protected) {
    $modal->enter();
}
$modal->close();
$modal->title([_t('Extensions'), $title]);
$modal->content = $form->render();

return $modal->response();

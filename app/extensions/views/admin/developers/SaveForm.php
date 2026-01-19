<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$form = Form::get();
if ($values) {
    $form->setValues($values);
    $form->hidden('id');
}
$form->input('developer_name')->setLabel(_t('Name'))->setRequired();
$form->input('project_url')->setLabel(_t('Project URL'));
$form->separate();
//
$form->input('webstore_url')->setLabel(_t('URL'));
$form->input('webstore_token')
    ->setLabel(_t('Token'))
    ->setHelp(_t('It is used to distribute extensions from this site. Manage a token in the webstore.'));
$form->separate(_t('Webstore'));
//
$form->element(_t('These values are used by default when creating new extensions.'));
$form->input('default_credits')->setLabel(_t('Credits'))->setRequired();
$form->input('default_license')->setLabel(_t('License'))->setRequired();
$form->separate(_t('Defaults'));

// modal
$modal = Modal::get();
if (!$is_protected) {
    $modal->enter();
}
$modal->close();
$modal->title([_t('Developers'), $title]);
$modal->content($form->render());

return $modal->response();

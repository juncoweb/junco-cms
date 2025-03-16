<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues($values);
$form->hidden('extension_alias');
$form->hidden('download_url');
//
$form->addRow(['content' => _t('Are you sure you want to download the file selected?')]);
if ($is_close) {
	$form->input('extension_key')->setLabel(_t('Key'));
	$form->hidden('is_close');
}
$form->toggle('install')->setLabel(_t('Install'));

// modal
$modal = Modal::get();
$modal->title($title);
$modal->enter(_t('Download'));
$modal->close();
//
$modal->content = $form->render()
	. '<div class="dialog dialog-warning">' . _t('If you choose not to install, the package will be available in the installer.') . '</div>';

return $modal->response();

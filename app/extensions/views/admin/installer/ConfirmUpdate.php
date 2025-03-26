<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues($values);
$form->hidden('update_id');
$form->hidden('download_url');
//
$form->addRow(['content' => _t('Are you sure you want to update the extension selected?')]);
if ($is_close) {
    $form->input('extension_key')->setLabel(_t('Key'));
    $form->hidden('_extension_key');
    $form->hidden('is_close', true);
}

// modal
$modal = Modal::get();
$modal->enter();
$modal->close();
$modal->title(_t('Update'));
$modal->content = $form->render();

return $modal->response();

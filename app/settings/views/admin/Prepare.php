<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues($values);

$form->group(
	$form->select('extension', $extensions)->setLabel(_t('Key')),
	$form->input('sub_extension')
);
$form->input('add_rows', ['type' => 'number'])->setLabel(_t('Total'));

// modal
$modal = Modal::get();
$modal->enter();
$modal->close();
$modal->title([_t('Manager'), _t('Create')]);
$modal->content = $form->render();

return $modal->response();

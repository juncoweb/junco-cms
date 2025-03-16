<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues(['delete' => true]);
$form->file('file')->setLabel(_t('Package'));
$form->checkbox('delete')->setLabel(_t('Delete original'));

// modal
$modal = Modal::get();
$modal->enter();
$modal->close();
$modal->title(_t('Upload package'));
$modal->content = $form->render();

return $modal->response();

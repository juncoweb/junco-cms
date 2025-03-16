<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues($values);
$form->hidden('role_id');
//
$form->input('role_name')->setLabel(_t('Name'));
$form->textarea('role_description', ['auto-grow' => ''])->setLabel(_t('Description'));

// modal
$modal = Modal::get();
$modal->enter();
$modal->close();
$modal->title([_t('Roles'), $title]);
$modal->content = $form->render();

return $modal->response();

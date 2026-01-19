<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$form = Form::get();
$form->setValues($values);
$form->hidden('id');
$form->hidden('extension_id');
//
$form->input('change_description')->setLabel(_t('Description'));
$form->checkbox('is_compatible')->setLabel(_t('Is compatible'));

// modal
$modal = Modal::get();
$modal->enter();
$modal->close();
$modal->title([_t('Changes'), $title]);
$modal->content($form->render());

return $modal->response();

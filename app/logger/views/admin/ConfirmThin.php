<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues(['delete' => true]);
$form->element(_t('Please, confirm the action.'));
$form->checkbox('delete')->setLabel(_t('Remove repeated'));

// modal
$modal = Modal::get();
$modal->enter(_t('Confirm'));
$modal->close();
$modal->title(_t('Check repeated'));
$modal->content = $form->render();

return $modal->response();

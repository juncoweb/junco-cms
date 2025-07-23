<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues(['status' => $status]);
$form->toggle('status')->setLabel(_t('Enable'));
$form->element('<div class="color-light text-center">' . _t('Enables the maintenance mode of the website.') . '</div>');

// modal
$modal = Modal::get();
$modal->enter();
$modal->close();
$modal->title(_t('Maintenance'));
$modal->content = $form->render();
$modal->form();

return $modal->response();

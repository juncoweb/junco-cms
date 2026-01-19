<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->hidden('language', $language);
//
$form->input('language_to')->setLabel(_t('Key'))->setRequired();

// modal
$modal = Modal::get();
$modal->close();
$modal->enter();
$modal->title(_t('Duplicate'));
$modal->content($form->render());

return $modal->response();

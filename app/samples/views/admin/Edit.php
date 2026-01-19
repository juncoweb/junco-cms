<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues($values);
$form->hidden('key');
//
$form->input('title')->setLabel(_t('Title'));
$form->input('image')->setLabel(_t('Image'));
$form->textarea('description')->setLabel(_t('Description'));

// modal
$modal = Modal::get();
$modal->close();
$modal->enter();
$modal->title('JSON Editor');
$modal->content($form->render());

return $modal->response();

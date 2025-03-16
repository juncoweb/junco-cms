<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->file('file')->setLabel(_t('File'));

// modal
$modal = Modal::get();
$modal->enter();
$modal->close();
$modal->title([_t('Language'), _t('Upload')]);
$modal->content = $form->render();

return $modal->response();

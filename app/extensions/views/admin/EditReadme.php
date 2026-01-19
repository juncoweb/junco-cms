<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
//

// actions
$fac = $form->getActions();
$fac->enter();
$fac->cancel();

// elements
$form->setValues($values);
$form->hidden('alias');
//
$form->editor('readme');

// modal
$modal = Modal::get();
$modal->title([$title, _t('Readme')]);
$modal->content($form->render());

return $modal->response();

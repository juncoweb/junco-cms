<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();

// actions
$fac = $form->getActions();
$fac->enter(_t('Save'));
$fac->cancel();

// elements
$form->setValues($values);
$form->textarea('contents', ['auto-grow' => '']);
$html = $form->render();
$html .= '<div class="dialog dialog-warning">' . _t('Edit the Javascript settings file.') . '</div>';

// modal
$modal = Modal::get();
$modal->title(_t('Options'));
$modal->content($html);

return $modal->response();

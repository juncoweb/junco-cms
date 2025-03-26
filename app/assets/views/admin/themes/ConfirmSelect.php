<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues($values);
$form->hidden('id');
//
if ($is_used) {
    $form->addRow(['content' => sprintf(_t('Confirm unselect the theme «%s» on the frontend.'), $theme)]);
} else {
    $form->addRow(['content' => sprintf(_t('Confirm select the theme «%s» on the frontend.'), $theme)]);
}

if ($explain_is_active) {
    $form->toggle('disable_explanation')
        ->setLabel(_t('Explain'))
        ->setHelp(_t('If you choose not to perform this action, the change may not be displayed.'));
}

// modal
$modal = Modal::get();
$modal->title(_t('Select'));
$modal->enter();
$modal->close();
$modal->content = $form->render();

return $modal->response();

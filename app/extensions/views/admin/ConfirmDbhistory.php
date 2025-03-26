<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();

// actions
$fac = $form->getActions();
if (!$is_protected) {
    $fac->enter();
}
$fac->cancel();

// elements
$form->setValues($values);
$form->hidden('id');

foreach ($queries as $row) {
    extract($row);
    //
    $form->header($Name, false);
    $form->input($key)->setLabel(_t('History'));

    if (!empty($db_history[$Type][$Name]['Fields'])) {
        $form->addRow([
            'label' => _t('Fields'),
            'content' => var_export($db_history[$Type][$Name]['Fields'], true)
        ]);
    }

    $form->addRow(['content' => '<hr />']);

    foreach ($Fields as $key => $Field) {
        $form->input($key)->setLabel($Field);
    }
    $form->separate();
}

// modal
$modal = Modal::get();
$modal->title([_t('DB history'), $title]);
$modal->content = $form->render();

return $modal->response();

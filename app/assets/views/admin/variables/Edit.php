<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();

// actions
$fac = $form->getActions();
if ($variables) {
    $fac->enter();
}
$fac->cancel();

// elements
$form->setValues($values);
$form->hidden('key');
$form->hidden('file');
//
$i = 0;
foreach ($variables as $row) {
    $form->group(
        $form->{$row['type']}('variables[' . $i . '][value]')->setLabel($row['name']),
        $form->{$row['type']}('variables[' . $i . '][default]', ['readonly' => ''])
    );

    $form->hidden('variables[' . $i . '][name]');
    $i++;
}

// modal
$modal = Modal::get();
$modal->title([_t('Layouts'), _t('Edit')]);
$modal->content = $form->render();

return $modal->response();

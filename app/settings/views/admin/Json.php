<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get('', false);
//

// actions
$fac = $form->getActions();
$fac->button('finish-save', _t('Finish & save'));
$fac->button('finish', _t('Finish'));
//$fac->cancel();

// elements
if ($options) {
    $count = 0;

    foreach ($json as $row) {
        $count++;
        $form->setDeep($row['deep']);
        $form->setValues($row['values']);
        //
        $form->header(
            $form->group(
                $form->input('__id')->setLabel("{$count}."),
                $form->button(['title' => _t('Delete'), 'control-form' => 'remove', 'icon' => 'fa-solid fa-xmark'])
            ),
            !$is_edit
        );

        foreach ($options as $name) {
            $form->input($name)->setLabel($name);
        }
        $form->separate();
    }
} else {
    if ($is_edit) {
        foreach ($json as $index => $row) {
            $form->setValues($row['values']);
            $form->group(
                $form->input($index)->setLabel($row['name']),
                $form->button(['title' => _t('Delete'), 'control-form' => 'remove', 'icon' => 'fa-solid fa-xmark'])
            );
        }
    } else {
        $form->input('name', ['style' => 'width:100px']);
        $label = $form->getLastElement();
        $form->input('value')->setLabel($label);
    }
}

$html = '<form id="js-form">' . $form->render() . '</form>';

// modal
$modal = Modal::get();
$modal->close();
$modal->title([_t('JSON'), $title]);
$modal->content($html);

return $modal->response();

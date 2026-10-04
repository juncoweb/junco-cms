<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->hidden('is_edit', $is_edit);

foreach ($values as $count => $_values) {
    $form->setDeep('[' . $count . ']');
    if ($_values) {
        $form->setValues($_values);
        $form->hidden('id');
    }
    //
    $form->columns(
        $form->select('extension_id', $extensions)->setLabel(_t('Extension'))->setRequired(),
        $form->input('menu_key')->setLabel(_t('Key'))
    );
    $form->columns(
        $form->input('menu_path')->setLabel(_t('Path')),
        $form->input('menu_order')->setLabel(_t('Order'))
    );
    $form->columns(
        $form->input('menu_url')->setLabel(_t('Url')),
        $form->input('menu_image')->setLabel(_t('Image'))
    );
    $form->columns(
        $form->input('menu_hash')->setLabel(_t('Hash')),
        $form->textarea('menu_params', ['auto-grow' => ''])->setLabel(_t('Parameters'))
    );
    $form->checkbox('status')->setLabel(_t('Public'));
    $form->separate();
}

// modal
$modal = Modal::get();
$modal->type($type);
$modal->enter();
$modal->close();
$modal->content($form->render());

return $modal->response();

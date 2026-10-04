<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->hidden('is_edit', $is_edit = ($type == 'edit'));

foreach ($values as $count => $_values) {
    $form->setDeep('[' . $count . ']');
    $form->setValues($_values);
    $form->hidden('label_id');
    //
    $form->header(
        $form->group(
            $form->select('extension_id', $extensions)->setLabel(sprintf('<b>%d.</b>', $count + 1))->setRequired(),
            $form->input('label_key')
        )
    );
    $form->input('label_name')->setLabel(_t('Name'));
    $form->textarea('label_description', ['auto-grow' => ''])->setLabel(_t('Description'));
    $form->separate();
}

// modal
$modal = Modal::get();
$modal->type($type);
$modal->enter();
$modal->close();
$modal->content($form->render());

return $modal->response();

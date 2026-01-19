<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues($values);
$form->hidden('extension_id');
//
$form->group(
    $form->select('extension_id', $extensions)
        ->setLabel(_t('Route'))
        ->setRequired(),
    $form->input('menu_subcomponent')
);
//$form->separate();
//
$form->input('menu_title')->setLabel(_t('Title'));
$form->input('menu_image')->setLabel(_t('Image'));
$form->select('menu_folder', $folders)
    ->setLabel(_t('Folder'))
    ->setHelp(_t('They are used in the backend and settings menus.'));
$form->checkboxList('menu_keys', $keys)->setLabel(_t('Keys'))->setRequired();
//$form->separate(_t('Options'));

// modal
$modal = Modal::get();
$modal->enter();
$modal->close();
$modal->title(_t('Maker'));
$modal->content($form->render());

return $modal->response();

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues($values);
//
$form->suite('extensions', $extensions)
    ->setLabel(_t('Extensions'))
    ->setHelp(_t('Append other extensions to the package.'));
$form->hidden('id');

// modal
$modal = Modal::get();
$modal->enter();
$modal->close();
$modal->title([_t('Append'), $title], 'fa-solid fa-share-nodes');
$modal->content($form->render());

return $modal->response();

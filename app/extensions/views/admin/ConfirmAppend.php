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
$modal->title([$title, _t('Append')]);
$modal->content($form->render());

return $modal->response();

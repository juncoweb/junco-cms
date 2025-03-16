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
$form->addRow(['content' => _t('Please, confirm compile the asset files.')]);
$form->toggle('minify')->setLabel(_t('Minify'));
$form->radio('fixurl', $fixurl_options)->setLabel('<span class="text-nowrap">' . _t('Fix url') . '</span>');

// modal
$modal = Modal::get();
$modal->title($_text = _t('Compile'));
$modal->enter($_text);
$modal->close();
$modal->content = $form->render();

return $modal->response();

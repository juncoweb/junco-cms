<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues($values);
$form->hidden('keys');
//
$form->addRow(['content' => _t('Please, confirm compile the asset files.')]);
$form->separate();
//
$form->checkbox('minify')->setLabel(_t('Minify'));
$form->separate(_t('Javascript or Css'));
//
$form->radio('fixurl', $fixurl_options)->setLabel('<span class="text-nowrap">' . _t('Fix url') . '</span>');
$form->select('precompile', $precompile_options)
    ->setLabel(_t('Precompile'))
    ->setHelp(_t('For each style sheet, try to find and run the precompiler.'));
$form->separate(_t('Only Css'));

// modal
$modal = Modal::get();
$modal->title($_text = _t('Compile'));
$modal->enter($_text);
$modal->close();
$modal->content = $form->render();

return $modal->response();

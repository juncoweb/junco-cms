<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues($values);
$form->hidden('id');
//
$form->toggle('minify')->setLabel(_t('Minify'));
$form->radio('fixurl', $fixurl_options)->setLabel('<span class="text-nowrap">' . _t('Fix url') . '</span>');

$html = '<p>' . _t('Please, confirm compile the asset files.') . '</p>';
$html .= $form->render();

// modal
$modal = Modal::get();
$modal->title($t = _t('Compile'));
$modal->enter($t);
$modal->close();
$modal->content($html);

return $modal->response();

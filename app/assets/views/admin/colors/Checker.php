<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */



// form
$form = Form::get();
//$form->setValues($values);
//
$form->input('color', ['control-felem' => 'color'])->setLabel(_t('Color'));
$form->button(['type' => 'button', 'id' => 'clean', 'label' => _t('Clear'), 'icon' => 'fa-solid fa-broom']);
$form->element('<div id="color-result" style="background: var(--theme-surface-color);"></div>');
$form->separate('');

$html = $form->render();

// template
$tpl = Template::get();
$tpl->options([
    'css' => 'cms/scripts/system/css/test-colors.css',
    'js' => 'app/assets/js/admin.colors.js',
    'thirdbar' => 'assets.colors',
    //'domready' => 'ColorChecker()'
]);
$tpl->title(_t('Checker'), 'fa-solid fa-square-check');
$tpl->content($html);

return $tpl->response();

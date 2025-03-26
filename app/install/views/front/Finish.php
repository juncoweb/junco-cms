<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues($values);
$form->hidden('goto');
$form->hidden('take');

// Actions
if ($bootstrap_is_writable) {
    $form->checkbox('remove_r')->setLabel(_t('Remove automatic redirection to the installer.'));
} else {
    $form->addRow(['content' => _t('Please, remove the automatic redirection from the bootstrap file.')]);
}

if ($install_is_writable) {
    $form->checkbox('remove_e')->setLabel(_t('Remove the installation extension.'));
} else {
    $form->addRow(['content' => _t('Please, remove the installation extension.')]);
}

$form->separate(_t('Actions'));

// redirect
$form->radio('redirect', [_t('Nothing'), _t('Site'), _t('Administration')], ['inline' => false])->setLabel();
$form->separate(_t('Redirect'));

//
$form->enter(_t('Finish'));
$form->separate(_t('Finish'));

// template
$tpl = Template::get('install');
$tpl->options(['hash' => 'finish']);
$tpl->title(sprintf(_t('Thank you %s for choosing us!'), $fullname));
$tpl->content = '<div class="dialog dialog-success">'
    . sprintf(_t('The Site %s has been installed correctly.'), '<b>' . $site_name . '</b>')
    . '</div>'
    . $form->render();

return $tpl->response();

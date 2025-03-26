<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues($values);
//
$form->header(_t('Site Data'));
$form->input('site_name')
    ->setLabel(_t('Name'))
    ->setHelp(_t('This name will appear identifying the site as required.'));

$form->input('site_url')
    ->setLabel(_t('URL'))
    ->setHelp(_t('Display full URL. Example: https://www.example.com/'));

$form->input('site_baseurl')
    ->setLabel(_t('Base URL'))
    ->setHelp(_t('The base url for all site resources (javascript, stylesheets, images, etc.)'));

$form->input('site_email')
    ->setLabel(_t('Email'))
    ->setHelp(_t('This is the default email to the Site'));
$form->separate();
//
$form->header(_t('Data Manager'));
$form->input('fullname')
    ->setLabel(_t('Name'))
    ->setHelp(_t('a-z A-Z 0-9'));

$form->input('username')
    ->setLabel(_t('Username'))
    ->setHelp(_t('az AZ 0-9 and dash, between 6 and 24 characters'));

$form->input('password', ['type' => 'password'])
    ->setLabel(_t('Password'))
    ->setHelp(_t('az AZ 0-9,between 6 and 24 characters'));

$form->input('email')
    ->setLabel(_t('Email'))
    ->setHelp(_t('Your personal email'));

// template
$tpl = Template::get('install');
$tpl->options([
    'hash' => 'settings',
    'submit' => true
]);
$tpl->title(_t('Settings'));
$tpl->content = $form->render();

return $tpl->response();

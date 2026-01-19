<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

if ($can_connect) {
    $html = '<div class="dialog dialog-success">' . _t('The Database connection is successful.') . '</div>';
} else {
    $adapters = [
        'mysql' => 'MySql',
        //'pgsql' => 'PostgreSql'
    ];

    // form
    $form = Form::get();
    $form->setValues($values);
    $form->select('db_adapter', $adapters)
        ->setLabel(_t('Adapter'))
        ->setHelp(_t('The use of postgres is only experimental. For production use myqsl.'));

    $form->toggle('use_pdo')
        ->setLabel(_t('Use PDO'));
    $form->separate(_t('General'));
    //
    $form->input('db_server')
        ->setLabel(_t('Host'))
        ->setHelp(_t('can be «localhost» or other name surely will be informed by your hosting provider.'));

    $form->input('db_username')
        ->setLabel(_t('Username'))
        ->setHelp(_t('can be «root» or other provided by your hosting.'));

    $form->input('db_password')
        ->setLabel(_t('Password'))
        ->setHelp(_t('It is important to place a password. The same may be provided by your hosting.'));

    $form->input('db_port')
        ->setLabel(_t('Port'))
        ->setHelp(_t('Keep zero to use the default value.'));
    $form->separate(_t('Connection'));
    //
    $form->input('db_database')
        ->setLabel(_t('Database'))
        ->setHelp(_t('Name of database. There are hosting where the base can only be created in his administration,in such cases,once created complete your name here.'));

    $form->select('db_collation', $collations)->setLabel(_t('Collation'))
        ->setHelp(_t('Select a collation option.'));

    $form->input('db_prefix')
        ->setLabel(_t('Prefix'))
        ->setHelp(_t('Prefix for the database fields.'));

    $form->separate(_t('Database'));
    $html = $form->render();
}

// template
$tpl = Template::get('install');
$tpl->options([
    'hash' => 'database',
    'submit' => !$can_connect
]);
$tpl->title(_t('Create Database'));
$tpl->content($html);

return $tpl->response();

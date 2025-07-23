<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

$YesNo = [
    '<span class="color-red bold">' . _t('No') . '</span>',
    '<span class="color-green bold">' . _t('Yes') . '</span>'
];

// form
$form = Form::get();
$form->element($YesNo[$min_php_version_result])
    ->setLabel(sprintf('PHP >= %s', $min_php_version))
    ->setHelp($min_php_version_result ? '' : sprintf(_t('Your version is %s.'), $php_version));

if ($max_php_version) {
    $form->element($YesNo[$max_php_version_result])
        ->setLabel(sprintf('PHP < %s', $max_php_version))
        ->setHelp($max_php_version_result ? '' : sprintf(_t('Your version is %s.'), $php_version));
}
$form->separate(_t('General'));

// libraries
$form->element($YesNo[$db_support])
    ->setLabel('MySQLi')
    ->setHelp($db_support ? '' : _t('Support for MySQLi databases.'));
$form->element($YesNo[$gd_support])
    ->setLabel('GD')
    ->setHelp($gd_support ? '' : _t('Support for editing images.'));
$form->separate(_t('PHP Libraries'));

// writable files
foreach ($writables as $row) {
    $form->element($YesNo[$row['is_writable']])
        ->setLabel($row['file'])
        ->setHelp($row['is_writable'] ?  '' : _t('Attention! This folder should be writable.'));
}
$form->separate(_t('Writable files'));

// template
$tpl = Template::get('install');
$tpl->options(['hash' => 'requirements']);
$tpl->title(_t('System Requirements'));
$tpl->content = '<p>' . _t('The following are the minimum requirements needed to function properly the site.') . '</p>'
    . $form->render();

return $tpl->response();

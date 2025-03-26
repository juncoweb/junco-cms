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
$form->addRow([
    'label' => sprintf('PHP >= %s', $min_php_version),
    'content' => $YesNo[$min_php_version_result],
    'help' => $min_php_version_result ? '' : sprintf(_t('Your version is %s.'), $php_version)
]);
if ($max_php_version) {
    $form->addRow([
        'label' => sprintf('PHP < %s', $max_php_version),
        'content' => $YesNo[$max_php_version_result],
        'help' => $max_php_version_result ? '' : sprintf(_t('Your version is %s.'), $php_version)
    ]);
}
$form->separate(_t('General'));

// libraries
$form->addRow([
    'label' => 'MySQLi',
    'content' => $YesNo[$db_support],
    'help' => $db_support ? '' : _t('Support for MySQLi databases.')
]);
$form->addRow([
    'label' => 'GD',
    'content' => $YesNo[$gd_support],
    'help' => $gd_support ? '' : _t('Support for editing images.')
]);
$form->separate(_t('PHP Libraries'));

// writable files
foreach ($writables as $row) {
    $form->addRow([
        'label' => $row['file'],
        'content' => $YesNo[$row['is_writable']],
        'help' => $row['is_writable'] ?  '' : _t('Attention! This folder should be writable.')
    ]);
}
$form->separate(_t('Writable files'));

// template
$tpl = Template::get('install');
$tpl->options(['hash' => 'requirements']);
$tpl->title(_t('System Requirements'));
$tpl->content = '<p>' . _t('The following are the minimum requirements needed to function properly the site.') . '</p>' . $form->render();

return $tpl->response();

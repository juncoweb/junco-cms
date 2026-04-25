<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$options = [
    _t('All'),
    _t('Only with updates')
];
if ($developer_mode) {
    $options[] = _t('Only packages');
    $options[] = _t('Only with changes');
}

// list
$bls = Backlist::get();

// filters
$filters = $bls->getFilters();
$filters->setValues($data);
$filters->select('option', $options);
$filters->select('status',  $statuses, $status);
$filters->select('developer_id', $developers);
$filters->search();

// table
if ($rows) {
    $bls->setRows($rows);
    $bls->setLabels('__labels');
    $bls->fixEnum('status');
}
//
$bls->check();
$bls->control('show')
    ->setText(':extension_name')
    ->setLabel(_t('Name'));

$bls->column(':developer_name')
    ->setSubtle();

$bls->button('update')
    ->setIcon('fa-solid fa-bolt', _t('Update'))
    ->keep('has_update');

if ($developer_mode) {
    $bls->button('distribute')
        ->setIcon('fa-solid fa-upload', _t('Distribute'))
        ->keep('package_exists');

    $bls->button('confirm_compile')
        ->setIcon('fa-solid fa-file-zipper', _t('Compile'))
        ->keep('can_compile');
}

$bls->status();

echo $bls->render($pagi);

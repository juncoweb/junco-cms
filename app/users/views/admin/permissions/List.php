<?php

/*
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 *
 */

// list
$bls = Backlist::get();

// filters
$filters = $bls->getFilters();
$filters->setValues($data);
$filters->select('role_id', $roles);
$filters->search();

// table
if ($rows) {
    $bls->setRows($rows);
    $bls->fixEnum('status', [
        ['color' => 'red', 'title' => _t('Disabled')],
        ['color' => 'green', 'title' => _t('Enabled')]
    ]);
}
//
$bls->check();
$bls->status('status');
$bls->column(':label_name')
    ->setLabel($roles[$role_id])
    ->setWidth(100)
    ->noWrap();

$bls->column(':label_description')
    ->setSubtle();

$bls->hidden('role_id', $role_id);

return $bls->render();

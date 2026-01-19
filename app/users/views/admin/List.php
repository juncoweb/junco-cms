<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// list
$bls = Backlist::get();

// filters
$filters = $bls->getFilters();
$filters->setValues($data);
$filters->searchIn([
    1 => _t('Name'),
    2 => _t('User'),
    3 => _t('Email')
]);
$filters->select('role_id', $roles);
$filters->sort($sort, $order);

// table
if ($rows) {
    $bls->setRows($rows);
    $bls->fixList('roles');
    $bls->fixDate('created_at', _t('Y-M-d'));
    $bls->fixEnum('status');
}
//
$bls->check();
$bls->column(':fullname')
    ->setLabel(_t('Name'), $filters);

$bls->column(':roles')
    ->setLabel(_t('Role'))
    ->setSubtle();

$bls->column(':created_at')
    ->setLabel(_t('Created'), $filters)
    ->setSubtle()
    ->setWidth(90)
    ->noWrap();

$bls->status('status');

return $bls->render($pagi);

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

if (!empty($error)) {
    return '<div class="dialog dialog-warning mt-4">' . $error . '</div>';
}

// list
$bls = Backlist::get();

// filters	
$filters = $bls->getFilters();
$filters->setValues($data);
$filters->search();

// table
if ($rows) {
    $bls->setRows($rows);
    $bls->fixDate('created_at', _t('Y-M-d'), _t('H:i:s'));
}
//
$bls->check();
$bls->column(':translation_code')
    ->setLabel(_t('Code'));

$bls->column(':developer_name')
    ->setLabel(_t('Developer'))
    ->setSubtle();

$bls->column(':created_at.date')
    ->setWidth(100)
    ->noWrap();

$bls->column(':created_at.time')
    ->setWidth(80)
    ->setSubtle()
    ->noWrap();

return $bls->render();

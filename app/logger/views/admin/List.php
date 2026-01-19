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
$filters->select('status', $statuses);
$filters->select('level', $levels);

// table
if ($rows) {
    $bls->setRows($rows);
    $bls->fixDate('created_at', _t('Y-M-d'), _t('H:i:s'));
    $bls->fixEnum('status');
}
//
$bls->check();
$bls->control('show')
    ->setText(':level')
    ->setLabel(_t('Level'))
    ->setWidth(60)
    ->setSubtle();

$bls->column('{{ message }}<div class="table-subtle-color only-on-large-screen">{{ file }}</div>');

$bls->column(':created_at.date')
    ->setLabel(_t('Date'))
    ->setWidth(80)
    ->noWrap();

$bls->column(':created_at.time')
    ->setLabel(_t('Time'))
    ->setWidth(50)
    ->noWrap()
    ->setSubtle();

$bls->status('status');

return $bls->render($pagi);

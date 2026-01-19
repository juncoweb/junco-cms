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
$filters->search();

// table
if ($rows) {
    foreach ($rows as $i => $row) {
        $rows[$i]['contact_message'] = Utils::cutText($row['contact_message']);
    }
    $bls->setRows($rows);
    $bls->fixDate('created_at', _t('Y-m-d'));
    $bls->fixenum('status');
}
//
$bls->check();
$bls->control('show')
    ->setText(':contact_name', _t('Show'))
    ->setLabel(_t('Contact'));

$bls->column(':contact_message')
    ->setSubtle();

$bls->column(':created_at')
    ->setLabel(_t('Date'))
    ->setSubtle()
    ->setWidth(80)
    ->noWrap();

$bls->status('status');

return $bls->render($pagi);

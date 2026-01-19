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
        $rows[$i]['job_error'] = Utils::cutText($row['job_error']);
    }

    $bls->setRows($rows);
    $bls->fixDate('created_at', _t('Y-M-d'), _t('H:i:s'));
}
//
$bls->check();
$bls->control('show')
    ->setText(':created_at.date', _t('Show'))
    ->setLabel(_t('Date'))
    ->setWidth(80)
    ->noWrap();

$bls->column(':created_at.time')
    ->setLabel(_t('Time'))
    ->setWidth(60)
    ->setSubtle()
    ->noWrap();

$bls->column(':job_queue')
    ->setLabel(_t('Queue'));

$bls->column(':job_error')
    ->setLabel(_t('Error'))
    ->setSubtle();

return $bls->render($pagi);

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
        $rows[$i]['reserved_at'] = $row['reserved_at'] === null
            ? 1
            : 0;
    }

    $bls->setRows($rows);
    $bls->fixDate('available_at', _t('Y-M-d'), _t('H:i:s'));
    $bls->fixEnum('reserved_at', [
        ['color' => 'danger', 'title' => _t('Reserved')],
        ['color' => 'warning', 'title' => _t('Waiting')],
    ]);
}
//
$bls->check();
$bls->control('show')
    ->setText(':available_at.date', _t('Show'))
    ->setLabel(_t('Date'))
    ->setWidth(80)
    ->noWrap();

$bls->column(':available_at.time')
    ->setLabel(_t('Time'))
    ->setWidth(60)
    ->setSubtle()
    ->noWrap();

$bls->column(':job_queue')
    ->setLabel(_t('Queue'));

$bls->column(':num_attempts')
    ->setLabel(_t('Attempts'))
    ->setSubtle()
    ->alignCenter();

$bls->column('<span class="badge badge-small badge-{{ reserved_at.color }}">{{ reserved_at.title }}</span>');

$bls->link(':failure_url')
    ->setIcon('fa-solid fa-arrow-right', _t('Failures'))
    ->keep('failure_url');

return $bls->render($pagi);

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
    $bls->setRows($rows);
    $bls->fixDate('created_at', _t('Y-M-d'));
}
//
$bls->check();
$bls->column(':notification_message')
    ->setLabel(_t('Message'));

$bls->column(':created_at')
    ->setSubtle()
    ->noWrap();

$bls->link(':url')
    ->setIcon('fa-solid fa-arrow-right')
    ->keep('url');

return $bls->render($pagi);

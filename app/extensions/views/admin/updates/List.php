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
    $bls->fixDate('released_at', _t('Y-M-d'));
    $bls->fixEnum('status');
}
//
$bls->check();
$bls->column(':extension_name')
    ->setLabel(_t('Extension'));

$bls->column(':update_version')
    ->setLabel(_t('Version'));

$bls->column(':released_at')
    ->setLabel(_t('Released'));

$bls->column(':has_failed')
    ->setLabel(_t('Failed'));

$bls->status();

return $bls->render($pagi);

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
    $bls->setLabels('__labels');
}
//
$bls->check();
$bls->column(':label_name')
    ->setLabel(_t('Name'));

$bls->column(':label_key')
    ->setLabel(_t('Key'))
    ->setSubtle();

return $bls->render($pagi);

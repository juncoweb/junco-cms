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
    $bls->fixEnum('is_protected', [
        ['title' => ''],
        ['title' => _t('Protected')],
    ]);
}
//
$bls->check();
$bls->column(':developer_name')
    ->setLabel(_t('Name'));

$bls->column(':is_protected.title')
    ->setSubtle()
    ->setWidth(120);

return $bls->render();

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
    2 => _t('Extension'),
]);

// table
if ($rows) {
    foreach ($rows as &$row) {
        if ($row['description']) {
            $row['description'] = ' <span class="table-subtle-color">' . Utils::cutText($row['description']) . '</span>';
        }
    }
    $bls->setRows($rows);
}
//
$bls->check(':key');
$bls->column('<i class="{{ image }}" aria-hidden="true"></i>')
    ->setWidth(20);

$bls->link(':url')
    ->setText(':title')
    ->setAfter(':description')
    ->setLabel(_t('Name'));

$bls->column(':extension')
    ->setLabel(_t('Extension'))
    ->setSubtle();

return $bls->render();

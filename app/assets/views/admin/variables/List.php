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
    $bls->fixRepeats('scope');
}
//
$bls->check(':file');
$bls->column(':name')
    ->setLabel(_t('Name'));

$bls->column(':scope')
    ->setSubtle();

$bls->hidden('key', $key);

return $bls->render();

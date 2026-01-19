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
$filters->checkbox('verify', _t('Verify'));
$filters->checkbox('compare', _t('Compare'));
$filters->select('type', $types);
$filters->search();

// table
if ($rows) {
    $bls->setRows($rows);
    $bls->fixEnum('to_verify', [
        ['color' => 'green', 'title' => _t('Ok')],
        ['color' => 'red', 'title' => _t('Verify')],
    ]);
}
//
$bls->check(':key');
$bls->column(':name')
    ->setLabel(_t('File'));

$bls->column(':type')
    ->setLabel(_t('Type'))
    ->setSubtle();

$bls->button('inspect')
    ->setIcon('fa-solid fa-circle color-{{ to_verify.color }}', ':to_verify.title');

return $bls->render($pagi);

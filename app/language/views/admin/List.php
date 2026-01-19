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
    $bls->fixEnum('selected', [
        'no' => ['color' => 'subtle-default', 'title' => _t('No')],
        'yes' =>  ['color' => 'default', 'title' => _t('Selected')]
    ]);
    $bls->fixEnum('status', [
        'disabled' => ['color' => 'red', 'title' => _t('Disabled')],
        'enabled' => ['color' => 'green', 'title' => _t('Enabled')]
    ]);
}
//
$bls->check();
$bls->control('domains')
    ->setText(':name')
    ->setLabel(_t('Name'));

$bls->column(':id')
    ->setLabel(_t('Key'))
    ->setSubtle();

$bls->button()
    ->setIcon('fa-solid fa-star color-{{ selected.color }}', ':selected.title');

$bls->status('status');

return $bls->render();

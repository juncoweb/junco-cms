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
    1 => _t('Path'),
    2 => _t('Extension'),
]);
$filters->select('menu_key', $menu_keys);

// table
if ($rows) {
    foreach ($rows as &$row) {
        $row['before'] = str_repeat('<span class="color-subtle-default">|—</span> ', $row['depth']);
    }

    $bls->setRows($rows);
    $bls->fixRepeats('menu_key');
    $bls->fixEnum('is_distributed', [
        ['icon' => 'fa-solid fa-link-slash table-subtle.color', 'title' => _t('Not distributed')],
        ['icon' => 'fa-solid fa-link', 'title' => _t('Is distributed')],
    ]);
    $bls->fixEnum('status');
}
//
$bls->check();
$bls->column(':menu_name')
    ->setLabel(_t('Name'))
    ->setBefore(':before');

$bls->column(':extension_name')
    ->setLabel(_t('Extension'))
    ->setSubtle();

$bls->column(':menu_key')
    ->setLabel(_t('Key'))
    ->setSubtle();

$bls->button('lock')
    ->setIcon(':is_distributed.icon', ':is_distributed.title');

$bls->status('status');

return $bls->render($pagi);

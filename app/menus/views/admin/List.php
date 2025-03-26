<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// list
$bls = Backlist::get();

// filters
$bft = $bls->getFilters();
$bft->setValues($data);
$bft->searchIn([
    1 => _t('Path'),
    2 => _t('Extension'),
]);
$bft->select('menu_key', $menu_keys);

// table
$bls->check_h();
$bls->th(_t('Name'));
$bls->th(_t('Extension'), ['priority' => 2]);
$bls->th(_t('Key'), ['priority' => 2]);
$bls->button_h('lock');
$bls->status_h();

if ($rows) {
    $distributed = [
        ['icon' => 'fa-solid fa-link-slash table-dimmed', 'title' => _t('Not distributed')],
        ['icon' => 'fa-solid fa-link', 'title' => _t('Is distributed')],
    ];

    foreach ($rows as $row) {
        $bls->check($row['id']);
        $bls->td(str_repeat('<span class="color-light">|—</span> ', $row['depth']) . $row['menu_name']);
        $bls->td($row['extension_name']);
        $bls->td($bls->isRepeated($row['menu_key']));
        $bls->button($distributed[$row['is_distributed']], !$row['is_protected']);
        $bls->status($row['status']);
    }
}

return $bls->render($pagi);

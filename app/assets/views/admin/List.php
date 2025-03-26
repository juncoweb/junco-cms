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
$bft->checkbox('verify', _t('Verify'));
$bft->checkbox('compare', _t('Compare'));
$bft->select('type', $types);
$bft->search();

// table
$bls->check_h();
$bls->th(_t('File'));
$bls->th(_t('Type'), ['priority' => 2]);
$bls->button_h('inspect');

if ($pagi->num_rows) {
    $statuses = [
        ['icon' => 'fa-solid fa-circle color-green', 'title' => _t('Ok')],
        ['icon' => 'fa-solid fa-circle color-red', 'title' => _t('Verify')],
    ];

    foreach ($pagi->fetchAll() as $row) {
        $bls->check($row['key']);
        $bls->td($row['name']);
        $bls->td($row['type']);
        $bls->button($statuses[$row['to_verify']]);
    }
}


return $bls->render($pagi);

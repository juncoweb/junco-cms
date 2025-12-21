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
$bft->search();

// table
$bls->check_h();
$bls->th(_t('Extension'));
$bls->th(_t('Version'));
$bls->th(_t('Released'));
$bls->th(_t('Failed'));
$bls->button_h(['control' => null, 'icon' => 'fa-solid fa-circle color-{{ color }}']);

foreach ($rows as $row) {
    $bls->check($row['id']);
    $bls->td($row['extension_name']);
    $bls->td($row['update_version']);
    $bls->td($row['released_at']->format($d ??= _t('Y-M-d')));
    $bls->td($row['has_failed']);
    $bls->button($row['status']);
}

return $bls->render($pagi);

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
    1 => _t('Name'),
    2 => _t('User'),
    3 => _t('Email')
]);
$bft->select('role_id', $roles);
$bft->sort($sort, $order);

// table
$bls->check_h();
$bls->th(_t('Name'), ['sort' => true]);
$bls->th(_t('Role'), ['priority' => 2]);
$bls->th(_t('Created'), ['priority' => 2, 'width' => 90, 'sort' => true, 'class' => 'text-nowrap']);
$bls->button_h(['control' => 'status', 'icon' => 'fa-solid fa-circle color-{{ color }}']);

if ($rows) {
    foreach ($rows as $row) {
        $bls->check($row['id']);
        $bls->td($row['fullname']);
        $bls->td(implode(', ', $row['roles']));
        $bls->td($row['created_at']->format(_t('Y-M-d')));
        $bls->button($row['status']);
    }
}

return $bls->render($pagi);

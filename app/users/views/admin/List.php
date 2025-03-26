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
$bls->button_h('status');

if ($rows) {
    $statuses = [
        'autosignup' => ['icon' => 'fa-solid fa-circle color-blue', 'title' => _t('Auto signup')],
        'inactive' => ['icon' => 'fa-solid fa-circle color-red', 'title' => _t('Inactive')],
        'active' => ['icon' => 'fa-solid fa-circle color-green', 'title' => _t('Active')],
    ];

    foreach ($rows as $row) {
        $bls->check($row['id']);
        $bls->td($row['fullname']);
        $bls->td(implode(', ', $row['roles']));
        $bls->td((new Date($row['created_at']))->format(_t('Y-M-d')));
        $bls->button($statuses[$row['status']]);
    }
}


return $bls->render($pagi);

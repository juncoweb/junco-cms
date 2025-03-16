<?php

/*
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 *
 */

// list
$bls = Backlist::get();

// filters
$bft = $bls->getFilters();
$bft->setValues($data);
$bft->select('role_id', $roles);
$bft->search();

$bls->check_h();
$bls->button_h('status');
$bls->th($roles[$role_id], ['width' => 100, 'class' => 'text-nowrap']);
$bls->th(['priority' => 2]);

foreach ($rows as $row) {
	$statuses = [
		['icon' => 'fa-solid fa-circle color-red', 'title' => _t('Disabled')],
		['icon' => 'fa-solid fa-circle color-green', 'title' => _t('Enabled')]
	];

	$bls->check($row['id']);
	$bls->button($statuses[(int)$row['status']]);
	$bls->td($row['label_name']);
	$bls->td($row['label_description'] ? _t($row['label_description']) : '');
}

$bls->hidden('role_id', $role_id);
return $bls->render();

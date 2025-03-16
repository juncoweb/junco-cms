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
$bls->link_h(_t('Name'), ['control' => 'domains']);
$bls->th(_t('Key'), ['priority' => 2]);
$bls->button_h(['icon' => '']);;
$bls->button_h('status');

if ($rows) {
	$selected = [
		'no' => ['icon' => 'fa-solid fa-star table-dimmed', 'title' => _t('No')],
		'yes' =>  ['icon' => 'fa-solid fa-star', 'title' => _t('Selected')],
	];
	$statuses = [
		'disabled' => ['icon' => 'fa-solid fa-circle color-red', 'title' => _t('Disabled')],
		'enabled' => ['icon' => 'fa-solid fa-circle color-green', 'title' => _t('Enabled')]
	];

	foreach ($rows as $row) {
		if ($row['status'] == 'enabled') {
			$bls->setLabel('enabled');
		}
		$bls->check($row['id']);
		$bls->link(['caption' => $row['name']]);
		$bls->td($row['id']);
		$bls->button($selected[$row['selected']]);
		$bls->button($statuses[$row['status']]);
	}
}
return $bls->render();

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
$bls->link_h([
	'control' => 'show',
	'title' => _t('Show'),
	'options' => ['content' => _t('Date'), 'width' => 80, 'class' => 'text-nowrap']
]);
$bls->th(_t('Time'), ['width' => 60, 'priority' => 2, 'class' => 'text-nowrap']);
$bls->th(_t('Queue'));
$bls->th(_t('Attempts'), ['priority' => 2, 'class' => 'text-center']);
$bls->th();
$bls->link_h(['icon' => 'fa-solid fa-arrow-right', 'title' => _t('Failures')]);

if ($rows) {
	$statuses = [
		'<span class="badge badge-danger">' . _t('Reserved') . '</span>',
		'<span class="badge badge-warning">' . _t('Waiting') . '</span>',
	];

	foreach ($rows as $row) {
		$bls->check($row['id']);
		$bls->link(['caption' => $row['available_at']->format($d ??= _t('Y-m-d'))]);
		$bls->td($row['available_at']->format('H-i-s'));
		$bls->td($row['job_queue']);
		$bls->td($row['num_attempts']);
		$bls->td($statuses[$row['reserved_at'] === null ? 1 : 0]);
		$bls->link($row['failure_url'], (bool)$row['failure_url']);
	}
}

return $bls->render($pagi);

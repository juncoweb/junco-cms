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
$bls->th(_t('Error'), ['priority' => 2]);

foreach ($rows as $row) {
	$bls->check($row['id']);
	$bls->link(['caption' => $row['created_at']->format($d ??= _t('Y-m-d'))]);
	$bls->td($row['created_at']->format('H-i-s'));
	$bls->td($row['job_queue']);
	$bls->td(Utils::cutText($row['job_error']));
}

return $bls->render($pagi);

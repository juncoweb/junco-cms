<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Users\UserActivityHelper;

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
$bft->select('type', $types);

// table
$bls->check_h();
$bls->th(_t('Type'), ['priority' => 2, 'width' => 90]);
$bls->th(_t('Code'), ['priority' => 2, 'width' => 60, 'class' => 'text-center']);
$bls->th(_t('Description'), ['priority' => 2]);
$bls->th(_t('Name'));
$bls->th(_t('Added'), ['priority' => 2, 'width' => 140]);

foreach ($pagi->fetchAll() as $row) {
	$date = new Date($row['created_at']);
	$row['created_at'] = $date->format(_t('Y-m-d H:i:s'));

	if (!$row['fullname']) {
		$row['fullname'] = inet_ntop($row['user_ip']);
	}

	if ($row['token_selector']) {
		$row['fullname'] .= '<div class="color-light">' . $row['token_selector'] . ' / ' . $row['status'] . '</div>';
		if ($row['modified_at']) {
			$row['created_at'] .= '<div class="color-light">' . $date->formatInterval($row['modified_at']) . '</div>';
		}
	}

	if ($row['activity_context']) {
		$row['fullname'] .= '<div class="color-light">' . $row['activity_context'] . '</div>';
	}

	$bls->check($row['id']);
	$bls->td($row['activity_type']);
	$bls->td($row['activity_code']);
	$bls->td(UserActivityHelper::getCodeMessages($row['activity_type'], $row['activity_code']));
	$bls->td($row['fullname']);
	$bls->td($row['created_at']);
}

return $bls->render($pagi);

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
$bls->th(_t('Status'));

if ($rows) {
	foreach ($rows as $row) {
		$bls->check($row['id']);
		$bls->td($row['extension_name']);
		$bls->td($row['update_version']);
		$bls->td($row['released_at']);
		$bls->td($row['has_failed']);
		$bls->td($row['status']);
	}
}

return $bls->render($pagi);

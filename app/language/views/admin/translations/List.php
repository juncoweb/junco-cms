<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

if (!empty($error)) {
	return '<div class="dialog dialog-warning mt-4">' . $error . '</div>';
}

// list
$bls = Backlist::get();

// filters	
$bft = $bls->getFilters();
$bft->setValues($data);
$bft->search();

// table
$bls->check_h();
$bls->th(_t('Code'));
$bls->th(_t('Developer'), ['priority' => 2]);
$bls->th(['width' => 100, 'class' => 'text-nowrap']);
$bls->th(['width' => 80, 'class' => 'text-nowrap', 'priority' => 2]);

if ($rows) {
	$_date = _t('Y-M-d');
	$_hour = _t('H:i:s');

	foreach ($rows as $row) {
		$row['created_at'] = new Date($row['created_at']);

		$bls->check($row['id']);
		$bls->td($row['translation_code']);
		$bls->td($row['developer_name']);
		$bls->td($row['created_at']->format($_date));
		$bls->td($row['created_at']->format($_hour));
	}
}

return $bls->render();

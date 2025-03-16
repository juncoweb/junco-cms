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
$bls->th(_t('Name'));
$bls->th(['priority' => 2, 'width' => 120]);

foreach ($pagi->fetchAll() as $row) {
	$bls->check($row['id']);
	$bls->td($row['developer_name']);
	$bls->td($row['is_protected'] ? _t('Protected') : false);
}

return $bls->render();

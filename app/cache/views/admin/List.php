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
$bls->th(_t('File'));

if ($keys) {
	foreach ($keys as $key) {
		$bls->check($key);
		$bls->td($key);
	}
}

return $bls->render();

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

foreach ($rows as $domain) {
	$bls->check($domain);
	$bls->td($domain);
}
$bls->hidden('language', $language);

return $bls->render();

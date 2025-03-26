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
$bls->th(['priority' => 2]);

foreach ($rows as $row) {
    $bls->check($row['file']);
    $bls->td($row['name']);
    $bls->td($bls->isRepeated($row['scope']));
}
$bls->hidden('key', $key);

return $bls->render();

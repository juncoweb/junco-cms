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

if ($rows) {
    foreach ($rows as $row) {
        $bls->check($row['id']);
        $bls->td($row['role_name']);
    }
}

return $bls->render($pagi);

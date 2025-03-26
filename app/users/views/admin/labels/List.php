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
$bls->th(_t('Key'), ['priority' => 2]);
//$bls->th(_t('Extension'), ['priority' => 2]);

foreach ($rows as $row) {
    if (!$row['is_protected']) {
        $bls->setLabel('editable');
    }
    $bls->check($row['id']);
    $bls->td($row['label_name']);
    $bls->td($row['label_key']);
    //$bls->td($row['extension_name']);
}

return $bls->render($pagi);

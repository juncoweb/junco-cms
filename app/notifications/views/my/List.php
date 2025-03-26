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
$bls->th(_t('Message'));
$bls->th(['priority' => 2, 'class' => 'text-nowrap']);
//$bls->status_h();
$bls->link_h(['icon' => 'fa-solid fa-arrow-right']);

foreach ($rows as $row) {
    $bls->check($row['id']);
    $bls->td($row['notification_message']);
    $bls->td($row['created_at']->format($dt ??= _t('Y-M-d')));
    //$bls->status($row['status']);
    $bls->link($row['url'], (bool)$row['url']);
}

return $bls->render($pagi);

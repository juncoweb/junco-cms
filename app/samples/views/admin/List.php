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
$bft->searchIn([
    1 => _t('Name'),
    2 => _t('Extension'),
]);

// table
$bls->check_h();
$bls->th(['width' => 20]);
$bls->link_h(_t('Name'));
$bls->th(_t('Extension'), ['priority' => 2]);

foreach ($rows as $row) {
    $bls->check($row['key']);
    $bls->td('<i class="' . $row['image'] . '"></i>');
    $bls->link([
        'url'     => $row['url'],
        'caption' => $row['title'],
        'after'   => $bls->body($row['description'])
    ]);
    $bls->td($row['extension']);
}

return $bls->render();

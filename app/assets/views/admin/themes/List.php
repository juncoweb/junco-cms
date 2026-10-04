<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */


// table
$bls = Backlist::get();

// table
if ($rows) {
    $bls->setRows($rows);
    $bls->fixEnum('is_default', [
        ['icon' => 'fa-regular fa-star table-subtle-color', 'title' => _t('Select')],
        ['icon' => 'fa-solid fa-star color-yellow', 'title' => _t('Default')]
    ]);
}
//
$bls->check(':key');
$bls->link(':url')
    ->setText(':key')
    ->setLabel(_t('Name'));

$bls->button('confirm_select')
    ->setIcon(':is_default.icon', ':is_default.title');

$bls->link(':url')
    ->setIcon('fa-solid fa-arrow-right');

return $bls->render();

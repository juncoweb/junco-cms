<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */


// table
$bls = Backlist::get();
$bls->check_h();
$bls->link_h(_t('Name'));
$bls->link_h(['icon' => 'fa-solid fa-arrow-right']);
$bls->button_h('confirm_select');

if ($rows) {
    $is_default = [
        ['icon' => 'fa-solid fa-minus table-dimmed', 'title' => _t('Select')],
        ['icon' => 'fa-solid fa-star', 'title' => _t('Default')]
    ];

    foreach ($rows as $row) {
        $bls->check($row['key']);
        $bls->link(['url' => $row['url'], 'caption' => $row['key']]);
        $bls->link($row['url']);
        $bls->button($is_default[$row['is_default']]);
    }
}

return $bls->render();

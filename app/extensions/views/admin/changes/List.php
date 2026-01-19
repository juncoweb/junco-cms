<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

//
$bls = Backlist::get();

// table
if ($rows) {
    foreach ($rows as &$row) {
        if ($row['status']) {
            $row['change_description'] = '<span class="color-subtle-default">' . $row['change_description'] . '</span>';
        }
    }

    $bls->setRows($rows);
    $bls->setLabels('__labels');
    $bls->fixDate('created_at', _t('Y-M-d'));
}
//
$bls->check();
$bls->column(':created_at')
    ->setSubtle()
    ->setWidth(80)
    ->noWrap();

$bls->column(':change_description');

$bls->hidden('extension_id', $extension_id);

return $bls->render($pagi);

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// list
$bls = Backlist::get();

// filters
$filters = $bls->getFilters();
$filters->setValues($data);
$filters->searchIn([
    1 => _t('Name'),
    2 => _t('User'),
    3 => _t('Email')
]);
$filters->select('type', $types);

// table
if ($rows) {
    foreach ($rows as &$row) {
        if ($row['activity_context']) {
            $row['fullname'] .= '<div class="color-subtle-default">' . $row['activity_context'] . '</div>';
        }

        if ($row['token_selector']) {
            $row['fullname'] .= '<div class="color-subtle-default">' . $row['token_selector'] . ' / ' . $row['status'] . '</div>';
        }
    }

    $bls->setRows($rows);
    $bls->fixDate('created_at', _t('Y-m-d H:i:s'));
}
//
$bls->check();
$bls->column(':activity_type')
    ->setLabel(_t('Type'))
    ->setSubtle()
    ->setWidth(90);

$bls->column(':activity_code')
    ->setLabel(_t('Code'))
    ->setSubtle()
    ->setWidth(60)
    ->alignCenter();

$bls->column(':message')
    ->setLabel(_t('Description'));

$bls->column(':fullname')
    ->setLabel(_t('Name'))
    ->setSubtle();

$bls->column(':created_at')
    ->setLabel(_t('Created'))
    ->setSubtle();

$bls->column(':modified_at')
    ->setSubtle();

return $bls->render($pagi);

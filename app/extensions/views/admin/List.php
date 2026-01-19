<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$options = [
    _t('All'),
    _t('Only with updates')
];
if ($developer_mode) {
    $options[] = _t('Only packages');
    $options[] = _t('Only with changes');
}

// list
$bls = Backlist::get();

// filters
$filters = $bls->getFilters();
$filters->setValues($data);
$filters->select('option', $options);
$filters->select('status',  $statuses, $status);
$filters->select('developer_id', $developers);
$filters->search();

// table
if ($rows) {
    $details_title = [_t('Developer'), _t('Version'), _t('Description'), _t('Credits'), _t('License'), _t('Website')];
    if ($developer_mode) {
        $details_title = array_merge($details_title, [_t('Components'), _t('Queries'), _t('Data')]);
    }

    foreach ($rows as $i => $row) {
        $content = [
            $row['developer_name'],
            $row['extension_version'],
            $row['extension_abstract'],
            $row['extension_credits'],
            $row['extension_license'],
            $row['project_url']
        ];

        if ($developer_mode) {
            $content = array_merge($content, [$row['components'], $row['db_queries'], $row['xdata']]);
        }

        $rows[$i]['details_data'] = htmlentities(json_encode([
            'title'   => $row['extension_name'],
            'content' => $content
        ]));
    }

    $bls->setRows($rows);
    $bls->setLabels('__labels');
    $bls->fixEnum('status');
}
//
$bls->check();
$bls->control('details')
    ->setText(':extension_name')
    ->setAfter('<div id="details-{{ id }}" style="display: none;">{{ details_data }}</div>')
    ->setLabel(_t('Name'))
    ->setAttr(['data-value' => 'details-{{ id }}']);

$bls->column(':developer_name')
    ->setSubtle();

$bls->button('update')
    ->setIcon('fa-solid fa-bolt', _t('Update'))
    ->keep('has_update');

if ($developer_mode) {
    $bls->button('distribute')
        ->setIcon('fa-solid fa-upload', _t('Distribute'))
        ->keep('package_exists');

    $bls->button('confirm_compile')
        ->setIcon('fa-solid fa-file-zipper', _t('Compile'))
        ->keep('can_compile');
}

$bls->status();

if ($rows) {
    $html = '<div id="details-caption" style="display: none;">' . json_encode($details_title) . '</div>';
} else {
    $html = '';
}

echo $html . $bls->render($pagi);

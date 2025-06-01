<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
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
$bft = $bls->getFilters();
$bft->setValues($data);
$bft->select('option', $options);
$bft->select('status',  $statuses, $status);
$bft->select('developer_id', $developers);
$bft->search();

// table
$bls->check_h();
$bls->link_h(_t('Name'), [
    'control' => 'details',
    'attr' => ['data-value' => 'details-{{ id }}']
]);
$bls->th(['priority' => 2]);
$bls->button_h('update', _t('Update'), 'fa-solid fa-bolt');
if ($developer_mode) {
    $bls->button_h('distribute', _t('Distribute'), 'fa-solid fa-upload');
    $bls->button_h('confirm_compile', _t('Compile'), 'fa-solid fa-file-zipper');
}
$bls->button_h(['control' => null, 'icon' => 'fa-solid fa-circle color-{{ color }}']);

if ($rows) {
    $details_title = [_t('Developer'), _t('Version'), _t('Description'), _t('Credits'), _t('License'), _t('Website')];
    if ($developer_mode) {
        $details_title = array_merge($details_title, [_t('Components'), _t('Queries'), _t('Data')]);
    }

    foreach ($rows as $row) {
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

            if (!$row['is_protected']) {
                $bls->setLabel('owner');
            }
            if ($row['can_compile']) {
                $bls->setLabel('package');
            }
        }
        $row['data'] = htmlentities(json_encode([
            'title'   => $row['extension_name'],
            'content' => $content
        ]));

        //
        $bls->check($row['id']);
        $bls->link([
            'id' => $row['id'],
            'caption' => $row['extension_name'],
            'after' => sprintf('<div id="details-%d" style="display: none;">%s</div>', $row['id'], $row['data'])
        ]);
        $bls->td($row['developer_name']);
        $bls->button([], $row['has_update']);

        if ($developer_mode) {
            $bls->button([], $row['package_exists']);
            $bls->button([], $row['can_compile']);
        }
        $bls->button($row['status']);
    }

    $html = '<div id="details-caption" style="display: none;">' . json_encode($details_title) . '</div>';
} else {
    $html = '';
}

echo $html . $bls->render($pagi);

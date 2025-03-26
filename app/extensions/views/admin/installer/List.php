<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// table
$bls = Backlist::get();
$bls->check_h();
$bls->button_h(['icon' => '']);;
$bls->link_h(_t('Packages'), [
    'control' => '{{ control }}'
]);
$bls->th(_t('Step (.. of 3)'), ['priority' => 2, 'class' => 'text-center']);
$bls->link_h([
    'control' => '{{ control }}',
    'options' => [
        'priority' => 2,
        //'class' => 'text-right'
    ]
]);
$bls->button_h(['control' => 'show_failure', 'icon' => '']);

if ($rows) {
    $icons = [
        ['icon' => 'fa-solid fa-download', 'title' => _t('Updating')],
        ['icon' => 'fa-solid fa-file-archive', 'title' => _t('Zipped package')],
        ['icon' => 'fa-solid fa-bolt', 'title' => _t('Package')],
    ];
    $steps = [
        ['control' => 'confirm_download', 'title' => _t('Download')],
        ['control' => 'confirm_unzip', 'title' => _t('Unzip')],
        ['control' => 'confirm_install', 'title' => _t('Install')],
    ];
    $statuses = [
        0 => ['icon' => 'fa-solid fa-circle color-green', 'title' => _t('Ok')],
        1 => ['icon' => 'fa-solid fa-circle-exclamation color-red', 'title' => _t('Failed')],
    ];

    foreach ($rows as $row) {
        /* if ($row['step'] == 0) {
			$bls->setlabel('updating');
		} */
        if ($row['has_failed']) {
            $bls->setlabel('failed');
        }
        $bls->check($row['id']);
        $bls->button($icons[$row['step']]);
        $bls->link($steps[$row['step']] + ['caption' => $row['caption']]);
        $bls->td(($row['step'] + 1));
        $bls->link($steps[$row['step']] + ['caption' => $steps[$row['step']]['title']]);
        $bls->button($statuses[$row['has_failed']]);
    }
}

return $bls->render();

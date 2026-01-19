<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

//
$bls = Backlist::get();

// table
if ($rows) {
    $bls->setRows($rows);
    $bls->setLabels('__labels');
    $bls->fixEnum('step', [
        [
            'number' => 1,
            'icon' => 'fa-solid fa-download',
            'title' => _t('Updating'),
            'control' => 'confirm_download',
            'action' => _t('Download')
        ],
        [
            'number' => 2,
            'icon' => 'fa-solid fa-file-archive',
            'title' => _t('Zipped package'),
            'control' => 'confirm_unzip',
            'action' => _t('Unzip')
        ],
        [
            'number' => 3,
            'icon' => 'fa-solid fa-bolt',
            'title' => _t('Package'),
            'control' => 'confirm_install',
            'action' => _t('Install')
        ],
    ]);
    $bls->fixEnum('has_failed', [
        ['color' => 'green', 'title' => _t('Ok')],
        ['color' => 'red', 'title' => _t('Failed')],
    ]);
}
//
$bls->check();
$bls->button()
    ->setIcon(':step.icon', ':step.title');

$bls->control(':step.control')
    ->setText(':caption', ':step.action')
    ->setLabel(_t('Packages'));

$bls->column(':step.number')
    ->setLabel(_t('Step (.. of 3)'))
    ->setSubtle()
    ->alignCenter();

$bls->control(':step.control')
    ->setText(':step.action', ':step.action')
    ->setSubtle();

$bls->status('show_failure', 'has_failed');

return $bls->render();

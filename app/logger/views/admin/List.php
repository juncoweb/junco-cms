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
$bft->select('status', [_t('Everything'), _t('Not checked'), _t('Checked'), _t('Repeated')]);
$bft->select('level', $levels);

// table
$bls->check_h();
$bls->link_h(_t('Level'), [
    'control' => 'show',
    'options' => ['width' => 60, 'priority' => 2, 'class' => 'text-uppercase']
]);
$bls->th();
$bls->th(_t('Date'), ['width' => 80, 'class' => 'text-nowrap']);
$bls->th(_t('Time'), ['width' => 50, 'class' => 'text-nowrap', 'priority' => 2]);
$bls->button_h('status');

if ($rows) {
    $statuses = [
        ['icon' => 'fa-solid fa-circle color-red', 'title' => _t('Not checked')],
        ['icon' => 'fa-solid fa-circle color-green', 'title' => _t('Checked')],
        ['icon' => 'fa-solid fa-circle', 'title' => _t('Repeated')]
    ];
    $d = _t('Y-M-d');
    $t = _t('H:i:s');

    foreach ($rows as $row) {
        $bls->check($row['id']);
        $bls->link(['caption' => $row['level']]);
        $bls->td($row['message'] . '<div class="table-dimmed only-on-large-screen">' . $row['file'] . '</div>');
        $bls->td($row['created_at']->format($d));
        $bls->td($row['created_at']->format($t));
        $bls->button($statuses[(int)$row['status']]);
    }
}

return $bls->render($pagi);

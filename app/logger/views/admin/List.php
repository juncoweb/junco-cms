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
$bft->select('status', $statuses);
$bft->select('level', $levels);

// table
$bls->check_h();
$bls->link_h(_t('Level'), [
    'control' => 'show',
    'options' => ['width' => 60, 'priority' => 2]
]);
$bls->th();
$bls->th(_t('Date'), ['width' => 80, 'class' => 'text-nowrap']);
$bls->th(_t('Time'), ['width' => 50, 'class' => 'text-nowrap', 'priority' => 2]);
$bls->button_h([
    'control' => 'status',
    'icon' => 'fa-solid fa-circle color-{{ color }}'
]);

foreach ($rows as $row) {
    $bls->check($row['id']);
    $bls->link(['caption' => $row['level']]);
    $bls->td($row['message'] . '<div class="table-dimmed only-on-large-screen">' . $row['file'] . '</div>');
    $bls->td($row['created_at']->format($d ??= _t('Y-M-d')));
    $bls->td($row['created_at']->format($t ??= _t('H:i:s')));
    $bls->button($row['status']);
}

return $bls->render($pagi);

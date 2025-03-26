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
$bft->search();

// table
$bls->check_h();
$bls->link_h(_t('Contact'), [
    'control' => 'show',
    'title' => _t('Show'),
]);
$bls->th(['priority' => 2]);
$bls->th(_t('Date'), ['priority' => 2, 'width' => 80, 'class' => 'text-nowrap']);
//$bls->th(['width' => 20]);
$bls->status_h();

foreach ($rows as $row) {
    /* if ($row['fullname']) {
		$row['fullname'] = '<i class="fa-solid fa-user" title="' . $row['fullname'] . '" aria-hidden="true"></i>';
	} else {
		$row['fullname'] = '';
	} */

    $bls->check($row['id']);
    $bls->link(['caption' => $row['contact_name']]);
    $bls->td(Utils::cutText($row['contact_message']));
    $bls->td($row['created_at']->format($d ??= _t('Y-m-d')));
    //$bls->td($row['fullname']);
    $bls->status($row['status']);
}

return $bls->render($pagi);

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// table
$bls = Backlist::get();
$bls->check_h();
$bls->th(['priority' => 2, 'width' => 80, 'class' => 'text-nowrap']);
$bls->th();

foreach ($pagi->fetchAll() as $row) {
    if ($row['status']) {
        $row['change_description'] = '<span class="color-light">' . $row['change_description'] . '</span>';
    }

    $bls->check($row['id'], !$row['status']);
    $bls->td((new Date($row['created_at']))->format(_t('Y-M-d')));
    $bls->td($row['change_description']);
}
$bls->hidden('extension_id', $extension_id);

return $bls->render($pagi);

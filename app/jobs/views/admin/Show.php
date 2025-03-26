<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

if ($reserved_at) {
    $reserved_at = $reserved_at->format(_t('Y-m-d'))
        . ' <span class="color-light">'
        . $reserved_at->format('H:i:s')
        . '</span>';
} else {
    $reserved_at = '<span class="badge badge-warning">' . _t('Waiting') . '</span>';
}

$html = '<table class="table table-bordered"><tbody>'
    . '<tr><th>' . _t('Available') . '</th><td>' . $available_at->format(_t('Y-m-d')) . ' <span class="color-light">' . $available_at->format('H:i:s') . '</span></td></tr>'
    . '<tr><th>' . _t('Created') . '</th><td>' . $created_at->format(_t('Y-m-d')) . ' <span class="color-light">' . $created_at->format('H:i:s') . '</span></td></tr>'
    . '<tr><th>' . _t('Reserved') . '</th><td>' . $reserved_at . '</td></tr>'
    . '<tr><th>' . _t('Queue') . '</th><td>' . $job_queue . '</td></tr>'
    . '<tr><th>' . _t('Attempts') . '</th><td>' . $num_attempts . '</td></tr>'
    //. '<tr><th>' . _t('Payload') . '</th><td>' . $job_payload . '</td></tr>'
    . '</tbody></table>';

// modal
$modal = Modal::get();
$modal->close();
$modal->title(_t('Failure'));
$modal->content = $html;

return $modal->response();

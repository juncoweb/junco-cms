<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

$html = '<table class="table table-bordered"><tbody>'
    . '<tr><th>' . _t('Date') . '</th><td>' . $created_at->format(_t('Y-m-d')) . ' <span class="color-light">' . $created_at->format('H:i:s') . '</span></td></tr>'
    . '<tr><th>' . _t('Queue') . '</th><td>' . $job_queue . '</td></tr>'
    . '<tr><th>' . _t('Id') . '</th><td>' . $job_id . '</td></tr>'
    . '<tr><th>' . _t('Uuid') . '</th><td>' . $job_uuid . '</td></tr>'
    //. '<tr><th>' . _t('Payload') . '</th><td>' . $job_payload . '</td></tr>'
    . '<tr><th>' . _t('Error') . '</th><td>' . nl2br($job_error) . '</td></tr>'
    . '</tbody></table>';

// modal
$modal = Modal::get();
$modal->close();
$modal->title(_t('Failure'));
$modal->content = $html;

return $modal->response();

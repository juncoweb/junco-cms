<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$zoom = Zoom::get();
$zoom->date($available_at)->setLabel(_t('Available'));
$zoom->date($created_at)->setLabel(_t('Created'));

if ($reserved_at) {
    $zoom->date($reserved_at)->setLabel(_t('Reserved'));
} else {
    $zoom->group('<span class="badge badge-warning">' . _t('Waiting') . '</span>')->setLabel(_t('Reserved'));
}
$zoom->group($job_queue)->setLabel(_t('Queue'));
$zoom->group($num_attempts)->setLabel(_t('Attempts'));
//$zoom->group($job_payload)->setLabel(_t('Payload'));

// modal
$modal = Modal::get();
$modal->close();
$modal->title(_t('Failure'));
$modal->content($zoom->render());

return $modal->response();

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$zoom = Zoom::get();
$zoom->columns(
    $zoom->date($created_at)->setLabel(_t('Created')),
    $zoom->group($job_queue)->setLabel(_t('Queue'))
);
$zoom->columns(
    $zoom->group($job_id)->setLabel(_t('Id')),
    $zoom->group($job_uuid)->setLabel(_t('Uuid'))
);
//$zoom->group($job_payload)->setLabel(_t('Payload'));
$zoom->group(nl2br($job_error))->setLabel(_t('Error'));

// modal
$modal = Modal::get();
$modal->close();
$modal->title(_t('Failure'));
$modal->content($zoom->render());

return $modal->response();

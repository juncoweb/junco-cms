<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$html = sprintf(_t('Please, confirm change the status to «%s».'), $status_title);

// modal
$modal = Modal::get();
$modal->title(_t('Status'));
$modal->enter();
$modal->close();
$modal->content($html);
//
$modal->getForm()
    ->hidden('id', $id)
    ->hidden('status', $status);

return $modal->response();

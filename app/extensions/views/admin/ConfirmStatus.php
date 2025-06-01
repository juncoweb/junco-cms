<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->title(_t('Status'));
$modal->enter();
$modal->close();
$modal->content = sprintf(_t('Please, confirm change the status to «%s».'), $status_title);
//
$modal->form();
$modal->form->hidden('id', $id);
$modal->form->hidden('status', $status);

return $modal->response();

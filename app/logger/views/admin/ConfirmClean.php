<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->enter(_t('Confirm'));
$modal->close();
$modal->title(_t('Clean log file'));
$modal->content = _t('Please, confirm the action.');
//
$modal->form();

return $modal->response();

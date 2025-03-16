<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->close();
$modal->enter(_t('Confirm'));
$modal->title(_t('Refresh'));
$modal->content = _t('Confirm to refresh the language cache.');
$modal->form();

return $modal->response();

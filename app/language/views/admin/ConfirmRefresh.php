<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->close();
$modal->enter(_t('Confirm'));
$modal->title(_t('Refresh'), 'fa-solid fa-arrows-rotate');
$modal->content(_t('Confirm to refresh the language cache.'));
$modal->getForm();

return $modal->response();

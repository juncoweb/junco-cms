<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->close();
$modal->enter(_t('Confirm'));
$modal->title(_t('Select'), 'fa-solid fa-flag');
$modal->content(_t('Please, confirm the action.'));
$modal->getForm()
    ->hidden('lang', $id);

return $modal->response();

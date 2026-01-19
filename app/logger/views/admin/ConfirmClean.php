<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->enter(_t('Confirm'));
$modal->close();
$modal->title(_t('Clean log file'), 'fa-solid fa-broom');
$modal->content(_t('Please, confirm the action.'));
//
$modal->getForm();

return $modal->response();

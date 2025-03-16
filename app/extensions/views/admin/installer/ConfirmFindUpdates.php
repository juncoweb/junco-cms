<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->enter(_t('Confirm'));
$modal->close();
$modal->title(_t('Find updates'));
$modal->content = _t('Please, confirm that you want to check for updates.');
$modal->form();

return $modal->response();

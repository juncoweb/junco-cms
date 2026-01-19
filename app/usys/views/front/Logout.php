<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->type('alert');
$modal->close();
$modal->enter(_t('Log out'));
$modal->title(_t('Confirm'));
$modal->content(_t('Are you sure you want to log out?'));
$modal->getForm('logout-form');

return $modal->response();

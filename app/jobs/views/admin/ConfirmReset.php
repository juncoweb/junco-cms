<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->title(_t('Reset'));
$modal->enter();
$modal->close();
$modal->content(_t('Please, confirm the action.'));
//
$modal->getForm()
    ->hidden('id', $id);

return $modal->response();

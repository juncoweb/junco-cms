<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->enter();
$modal->close();
$modal->title([_t('Unzip'), $id]);
$modal->content(_t('Are you sure you want to unzip the file selected?'));
//
$modal->getForm()
    ->hidden('file', $id);

return $modal->response();

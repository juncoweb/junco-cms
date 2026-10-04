<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->enter($t = _t('Download'));
$modal->close();
$modal->title($t, 'fa-solid fa-download');
$modal->content(_t('Are you sure you want to download the file selected?'));
//
$modal->getForm()
    ->hidden('id', $id);
//
return $modal->response();

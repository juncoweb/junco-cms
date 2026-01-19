<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->type('alert');
$modal->title($t = _t('Delete'), 'fa-solid fa-trash');
$modal->enter($t);
$modal->close();
//
$modal->getForm()
    ->question($id)
    ->hidden('id', $id);

return $modal->response();

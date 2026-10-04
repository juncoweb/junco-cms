<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->type('status', $status);
$modal->enter();
$modal->close();
//
$modal->getForm()
    ->question($id, 'status')
    ->hidden('id', $id)
    ->hidden('status', $status['name']);

return $modal->response();

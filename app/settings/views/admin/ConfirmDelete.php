<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->type('delete');
$modal->enter();
$modal->close();
//
$modal->getForm()
    ->question(1)
    ->hidden('key', $key);

return $modal->response();

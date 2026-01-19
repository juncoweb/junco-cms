<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->type('alert');
$modal->title($t = _t('Delete'), 'fa-solid fa-trash');
if (!$warning) {
    $modal->enter($t);
}
$modal->close();
//
if ($warning) {
    $html = '';
    foreach ($warning as $message) {
        $html .= '<p class="dialog dialog-warning">' . $message . '</p>';
    }

    $modal->content('<p class="dialog dialog-warning">' . $message . '</p>');
} else {
    $modal->getForm()
        ->question($id)
        ->hidden('developer_id', $id);
}

return $modal->response();

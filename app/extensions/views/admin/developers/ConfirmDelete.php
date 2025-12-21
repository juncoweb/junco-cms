<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->type('alert');
$modal->title($_text = _t('Delete'), 'fa-solid fa-trash');
if (!$warning) {
    $modal->enter($_text);
}
$modal->close();
//
if ($warning) {
    foreach ($warning as $message) {
        $modal->content .= '<p class="dialog dialog-warning">' . $message . '</p>';
    }
} else {
    $modal->form();
    $modal->form->question($id);
    $modal->form->hidden('developer_id', $id);
}


return $modal->response();

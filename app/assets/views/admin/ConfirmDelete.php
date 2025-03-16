<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->type('alert');
$modal->title($_text = _t('Delete'), 'fa-solid fa-trash-can');
$modal->enter($_text);
$modal->close();
//
$modal->form();
$modal->form->question($id);
$modal->form->hidden('keys', $id);

return $modal->response();

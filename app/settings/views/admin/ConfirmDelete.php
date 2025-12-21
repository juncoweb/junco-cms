<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->type('alert');
$modal->title($_text = _t('Delete'), 'fa-solid fa-trash');
$modal->enter($_text);
$modal->close();
//
$modal->form();
$modal->form->question(1);
$modal->form->hidden('key', $key);

return $modal->response();

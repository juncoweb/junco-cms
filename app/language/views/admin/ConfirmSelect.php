<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->close();
$modal->enter(_t('Confirm'));
$modal->title(_t('Select'));
$modal->content = _t('Please, confirm the action.');
$modal->form();
$modal->form->hidden('lang', $id);

return $modal->response();

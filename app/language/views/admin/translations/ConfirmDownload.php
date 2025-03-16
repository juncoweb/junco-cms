<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->enter($text = _t('Download'));
$modal->close();
$modal->title($text);
$modal->content = _t('Are you sure you want to download the file selected?');
//
$modal->form();
$modal->form->hidden('id', $id);
//
return $modal->response();

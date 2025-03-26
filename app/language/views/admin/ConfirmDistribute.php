<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// modal
$modal = Modal::get();
$modal->close();
$modal->enter();
$modal->title(_t('Distribute'));
$modal->content = '<p>' . _t('Are you sure you want to distribute the language?') . '</p>'
    . '<div class="dialog dialog-warning">' . _t('This distribution will replace any previous distribution.') . '</div>';
//
$modal->form();
$modal->form->hidden('language', $id);
//
return $modal->response();

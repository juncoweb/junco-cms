<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$html = '<p>' . _t('Are you sure you want to distribute the language?') . '</p>';
$html .= '<div class="dialog dialog-warning">' . _t('This distribution will replace any previous distribution.') . '</div>';


// modal
$modal = Modal::get();
$modal->close();
$modal->enter();
$modal->title(_t('Distribute'));
$modal->content($html);
//
$modal->getForm()
    ->hidden('language', $id);
//
return $modal->response();

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox('', 'translations');
$bac = $bbx->getActions();
$bac->button('confirm_download', _t('Download'), 'fa-solid fa-download');
$bac->filters();
$bac->refresh();

// modal
$modal = Modal::get();
$modal->close();
$modal->title(_t('Translations'));
$modal->content = $bbx->render($this->list($data));

return $modal->response();

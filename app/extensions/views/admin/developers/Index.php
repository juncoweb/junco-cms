<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox('', 'developers');
$bac = $bbx->getActions();
$bac->create();
$bac->edit();
$bac->delete();
$bac->filters();

// modal
$modal = Modal::get();
$modal->close();
$modal->title(_t('Developers'));
$modal->content = $bbx->render($this->list());

return $modal->response();

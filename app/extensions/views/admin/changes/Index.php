<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox('', 'changes');
$bac = $bbx->getActions();
$bac->create();
$bac->edit();
$bac->delete();
$bac->refresh();

// modal
$modal = Modal::get();
$modal->close();
$modal->title([_t('Changes'), $title], 'fa-regular fa-file-lines');
$modal->content($bbx->render($this->list($data)));

return $modal->response();

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
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
$modal->title([$title, _t('Changes')]);
$modal->content = $bbx->render($this->list($data));

return $modal->response();

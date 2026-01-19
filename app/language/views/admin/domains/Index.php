<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox('', 'domains');
$bac = $bbx->getActions();
$bac->filters();
$bac->refresh();

// modal
$modal = Modal::get();
$modal->close();
$modal->title([_t('Domains'), $title]);
$modal->content($bbx->render($this->list($data)));

return $modal->response();

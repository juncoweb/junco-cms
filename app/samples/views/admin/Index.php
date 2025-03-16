<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
$bac->edit();
$bac->separate();
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->js('assets/samples-admin.min.js');
$tpl->domready('AdminTools.List()');
$tpl->title(_t('Samples'), 'fa-solid fa-toolbox');
$tpl->content = $bbx->render();

return $tpl->response();

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
$bac->create(1);
$bac->edit();
$bac->delete();
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->js('assets/users-admin.min.js');
$tpl->domready('UsersLabels.List()');
$tpl->title(_t('Labels'), 'fa-solid fa-tag');
$tpl->content = $bbx->render();

return $tpl->response();

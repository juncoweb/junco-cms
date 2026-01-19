<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
$bac->create();
$bac->edit();
$bac->delete();
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->js('assets/users-admin.min.js');
$tpl->domready('UsersRoles.List()');
$tpl->title(_t('Roles'), 'fa-solid fa-user-gear');
$tpl->content($bbx->render());

return $tpl->response();

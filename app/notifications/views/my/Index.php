<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
//$bac->toggle();
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->options([
	'load' => 'myspace',
	'js' => 'assets/notifications-myspace.min.js',
	'domready' => 'MyNotifications.List()'
]);
$tpl->title(_t('Notifications'), 'fa-solid fa-bell');
$tpl->content = $bbx->render();

return $tpl->response();

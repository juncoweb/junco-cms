<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
$bac->toggle();
$bac->delete();
$bac->dropdown([
	['control' => 'show', 'label' => _t('Info'), 'icon' => 'fa-solid fa-circle-info'],
	[],
	['control' => 'confirm_thin', 'label' => _t('Check repeated'), 'icon' => 'fa-solid fa-check'],
	['control' => 'confirm_clean', 'label' => _t('Clean log file'), 'icon' => 'fa-solid fa-broom'],
	[],
	['control' => 'confirm_report', 'label' => _t('Report bugs'), 'icon' => 'fa-solid fa-bug'],
]);
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->js('assets/logger-admin.min.js');
$tpl->domready('Logger.List()');
$tpl->title(_t('Logger'), 'fa-solid fa-bug');
$tpl->content = $bbx->render();

return $tpl->response();

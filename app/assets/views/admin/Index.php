<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
$bac->create();
$bac->edit();
$bac->button('confirm_compile', _t('Compile'), 'fa-solid fa-gears');
$bac->dropdown([
	['control' => 'confirm_delete', 'label' => _t('Delete'), 'icon' => 'fa-solid fa-trash-can'],
	['control' => 'inspect', 'label' => _t('Inspect'), 'icon' => 'fa-solid fa-code'],
	[],
	//['control' => 'themes', 'label' => _t('Themes'), 'icon' => 'fa-solid fa-gear'],
	['control' => 'confirm_options', 'label' => _t('Options'), 'icon' => 'fa-solid fa-gear'],
]);
$bac->separate();
//$bac->button(url('admin/assets.themes'), _t('Themes'), 'fa-solid fa-palette');
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->js('assets/assets-admin.min.js');
$tpl->domready('AdminAssets.List()');
$tpl->title(_t('Assets'), 'fa-regular fa-circle');
$tpl->content = $bbx->render();

return $tpl->response();

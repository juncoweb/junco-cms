<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
//$bac->create();
//$bac->edit();
//$bac->toggle();
/* $bac->toggle([
	['control' => 'status', 'value' => 1, 'label' => _t('Enabled')],
	['control' => 'status', 'value' => 0, 'label' => _t('Disabled')],
]); */
//$bac->delete();
//$bac->button('button', _t('Button'), 'fa-solid fa-tag');
/* $bac->dropdown([
	['control' => 'confirm_delete', 'label' => _t('Delete'), 'icon' => 'fa-solid fa-trash'],
]); */
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
//$tpl->editor();
/*$tpl->options([
	'load' => 'myspace',
	'js' => 'app/extensions/js/admin.updates.js',
	'domready' => 'ExtensionsUpdates.List()'
]);*/
$tpl->js('app/extensions/js/admin.updates.js');
$tpl->domready('ExtensionsUpdates.List()');
$tpl->title(_t('Updates'), 'fa-solid fa-file-lines');
$tpl->content = $bbx->render();

return $tpl->response();

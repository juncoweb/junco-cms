<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
/* $bac->toggle([
	['control' => 'status', 'value' => 1, 'label' => _t('Enabled')],
	['control' => 'status', 'value' => 0, 'label' => _t('Disabled')],
]); */
//$bac->delete();
$bac->button('show', _t('Show'), 'fa-solid fa-eye');
/* $bac->dropdown([
	['control' => 'confirm_trash', 'label' => _t('Delete')],
]); */
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->js('assets/jobs-admin.min.js');
$tpl->domready('JobsFailures.List()');
$tpl->title([_t('Jobs'), _t('Failures')], 'fa-solid fa-bug');
$tpl->content($bbx->render());

return $tpl->response();

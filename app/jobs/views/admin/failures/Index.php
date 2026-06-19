<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
$bac->back($back_url);
$bac->separate();
//
$bac->button('show', _t('Show'), 'fa-solid fa-eye');
//
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->js('assets/jobs-admin.min.js');
$tpl->domready('JobsFailures.List()');
$tpl->title([_t('Jobs'), _t('Failures')], 'fa-solid fa-bug');
$tpl->content($bbx->render());

return $tpl->response();

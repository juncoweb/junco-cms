<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
$bac->delete();
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->js('assets/cache-admin.min.js');
$tpl->domready('Cache.List()');
$tpl->title(_t('Cache'), 'fa-solid fa-bolt');
$tpl->content($bbx->render());

return $tpl->response();

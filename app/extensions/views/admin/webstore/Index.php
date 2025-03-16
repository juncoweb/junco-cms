<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->css('assets/extensions-admin.min.css');
$tpl->js('assets/extensions-admin.min.js');
$tpl->domready('Webstore.List()');
$tpl->title(_t('Web Store'), 'fa-solid fa-bag-shopping');
$tpl->content = $bbx->render();

return $tpl->response();

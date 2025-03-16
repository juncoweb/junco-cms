<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
$bac->back(url('admin/assets.themes'));
$bac->separate();
$bac->edit();
//
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->js('assets/assets-admin.min.js');
$tpl->domready('AssetsVariables.List(\'' . $key . '\')');
$tpl->title([_t('Themes'), $title], 'fa-solid fa-palette');
$tpl->content = $bbx->render();

return $tpl->response();

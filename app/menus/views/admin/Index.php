<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
$bac->create(1);
//$bac->edit();
//$bac->button('copy', _t('Copy'), 'fa-solid fa-copy');
$bac->toggle();
//$bac->delete();
$bac->dropdown([
    ['control' => 'edit', 'label' => _t('Edit'), 'icon' => 'fa-solid fa-pencil'],
    ['control' => 'copy', 'label' => _t('Copy'), 'icon' => 'fa-solid fa-copy'],
    ['control' => 'confirm_delete', 'label' => _t('Delete'), 'icon' => 'fa-solid fa-trash'],
    [],
    ['control' => 'confirm_maker', 'label' => _t('Maker'), 'icon' => 'fa-solid fa-hammer'],
]);
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->js('assets/menus-admin.min.js');
$tpl->domready('Menus.List()');
$tpl->title(_t('Menus'), 'fa-solid fa-bars');
$tpl->content($bbx->render());

return $tpl->response();

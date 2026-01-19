<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
$bac->create();
$bac->button('copy', _t('Copy'), 'fa-solid fa-copy');
$bac->button('confirm_compile', _t('Compile'), 'fa-solid fa-gears');
$bac->dropdown([
    ['control' => 'confirm_delete', 'label' => _t('Delete'), 'icon' => 'fa-solid fa-trash'],
    [],
    ['control' => 'confirm_select', 'label' => _t('Select'), 'icon' => 'fa-solid fa-star'],
]);
$bac->refresh();

// template
$tpl = Template::get();
$tpl->js('assets/assets-admin.min.js');
$tpl->domready('AssetsThemes.List()');
$tpl->title(_t('Themes'), 'fa-solid fa-palette');
$tpl->content($bbx->render());

return $tpl->response();

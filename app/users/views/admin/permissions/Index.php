<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
$bac->toggle([
    ['control' => 'status', 'value' => 2, 'label' => _t('Enabled')],
    ['control' => 'status', 'value' => 1, 'label' => _t('Disabled')],
]);
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->js('assets/users-admin.min.js');
$tpl->domready('Permissions.List()');
$tpl->title(_t('Permissions'), 'fa-solid fa-check');
$tpl->content = $bbx->render();

return $tpl->response();

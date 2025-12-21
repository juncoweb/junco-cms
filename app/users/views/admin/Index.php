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
$bac->toggle(array_map(fn($case) => [
    'control' => 'status',
    'value' => $case->name,
    'label' => $case->title()
], $statuses));
$bac->dropdown([
    ['control' => 'edit', 'label' => _t('Edit'), 'icon' => 'fa-solid fa-pencil'],
    ['control' => 'confirm_delete', 'label' => _t('Delete'), 'icon' => 'fa-solid fa-trash'],
]);
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->js('assets/users-admin.min.js');
$tpl->domready('Users.List()');
$tpl->title(_t('Users'), 'fa-solid fa-users');
$tpl->content = $bbx->render();

return $tpl->response();

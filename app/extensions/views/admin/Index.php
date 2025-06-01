<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
if ($developer_mode) {
    $bac->create();
    $bac->edit();
    $bac->toggle(array_map(fn($case) => [
        'control' => 'confirm_status',
        'name' => 'status',
        'value' => $case->name,
        'label' => $case->title()
    ], $statuses));
    $bac->dropdown([
        ['control' => 'confirm_delete', 'label' => _t('Delete'), 'icon' => 'fa-solid fa-trash-can'],
        ['control' => 'changes', 'label' => _t('Changes'), 'icon' => 'fa-regular fa-file-lines'],
        ['control' => 'confirm_dbhistory', 'label' => _t('DB history'), 'icon' => 'fa-solid fa-clock-rotate-left'],
        ['control' => 'edit_readme', 'label' => _t('Readme'), 'icon' => 'fa-solid fa-file-lines'],
        [],
        ['control' => 'confirm_append', 'label' => _t('Append'), 'icon' => 'fa-solid fa-share-nodes'],
        ['control' => 'confirm_compile', 'label' => _t('Compile'), 'icon' => 'fa-solid fa-file-zipper'],
        [],
        ['control' => 'confirm_find_updates', 'label' => _t('Find updates'), 'icon' => 'fa-solid fa-arrows-rotate'],
        ['control' => 'confirm_update_all', 'label' => _t('Update all'), 'icon' => 'fa-solid fa-bolt'],
        [],
        ['control' => 'developers', 'label' => _t('Developers'), 'icon' => 'fa-solid fa-user-check'],
    ]);
} else {
    $bac->dropdown([
        ['control' => 'confirm_find_updates', 'label' => _t('Find updates'), 'icon' => 'fa-solid fa-arrows-rotate'],
        ['control' => 'confirm_update_all', 'label' => _t('Update all'), 'icon' => 'fa-solid fa-bolt'],
    ]);
}
//
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->editor();
$tpl->css('assets/extensions-admin.min.css');
$tpl->js('assets/extensions-admin.min.js');
$tpl->domready('Extensions.List()');
$tpl->title(_t('Extensions'), 'fa-solid fa-puzzle-piece');
$tpl->content = $bbx->render();

return $tpl->response();

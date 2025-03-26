<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
$bac->delete();
$bac->dropdown([
    ['control' => 'confirm_find_updates', 'label' => _t('Find updates'), 'icon' => 'fa-solid fa-arrows-rotate'],
    ['control' => 'confirm_update_all', 'label' => _t('Update all'), 'icon' => 'fa-solid fa-bolt'],
    ['control' => 'confirm_upload', 'label' => _t('Upload package'), 'icon' => 'fa-solid fa-upload'],
    [],
    ['control' => 'confirm_maintenance', 'label' => _t('Maintenance'), 'icon' => 'fa-solid fa-hammer'],
]);
$bac->refresh();

// template
$tpl = Template::get();
$tpl->css('assets/extensions-admin.min.css');
$tpl->js('assets/extensions-admin.min.js');
$tpl->domready('Installer.List()');
$tpl->title([_t('Extensions'), _t('Installer')], 'fa-solid fa-puzzle-piece');
$tpl->content = $bbx->render();

return $tpl->response();

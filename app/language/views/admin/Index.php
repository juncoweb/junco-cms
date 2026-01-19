<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
$bac->button('confirm_select', _t('Select'), 'fa-solid fa-flag');
$bac->edit();
//$bac->button('confirm_duplicate', _t('Duplicate'), 'fa-solid fa-copy');
//$bac->delete();
$bac->dropdown([
    //['control' => 'confirm_select', 'label' => _t('Select'), 'icon' => 'fa-solid fa-flag'],
    ['control' => 'confirm_duplicate', 'label' => _t('Duplicate'), 'icon' => 'fa-solid fa-copy'],
    ['control' => 'confirm_delete', 'label' => _t('Delete'), 'icon' => 'fa-solid fa-trash'],
    [],
    ['control' => 'confirm_refresh', 'label' => _t('Refresh'), 'icon' => 'fa-solid fa-arrows-rotate'],
    [],
    ['control' => 'confirm_import', 'label' => _t('Import'), 'icon' => 'fa-solid fa-file-import'],
    ['control' => 'confirm_export', 'label' => _t('Export'), 'icon' => 'fa-solid fa-file-export'],
    [],
    ['control' => 'confirm_distribute', 'label' => _t('Distribute'), 'icon' => 'fa-solid fa-upload'],
    ['control' => 'translations', 'label' => _t('Translations'), 'icon' => 'fa-solid fa-language'],
]);
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->editor();
$tpl->js('assets/language-admin.min.js');
$tpl->domready('Language.List()');
$tpl->title(_t('Language'), 'fa-solid fa-flag');
$tpl->content($bbx->render());

return $tpl->response();

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$bbx = Backlist::getBox();

// actions
$bac = $bbx->getActions();
$bac->button('show', _t('Show'), 'fa-solid fa-eye');
$bac->separate();
$bac->button(url('admin/jobs.failures'), _t('Failures'), 'fa-solid fa-bug');
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->js('assets/jobs-admin.min.js');
$tpl->domready('Jobs.List()');
$tpl->title(_t('Jobs'), 'fa-solid fa-hammer');
$tpl->content = $bbx->render();

return $tpl->response();

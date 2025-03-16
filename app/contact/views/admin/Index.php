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
$bac->toggle();
$bac->delete();
$bac->separate();
$bac->filters();
$bac->refresh();

// template
$tpl = Template::get();
$tpl->editor();
$tpl->js('assets/contact-admin.min.js');
$tpl->domready('Contact.List()');
$tpl->title(_t('Contact'), 'fa-solid fa-envelope');
$tpl->content = $bbx->render();

return $tpl->response();

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// box
$fbox = Form::getBox();
$fbox->tab('', $this->form());

// template
$tpl = Template::get();
$tpl->editor();
$tpl->js('assets/email-admin.min.js');
$tpl->domready('Email.write();');
$tpl->title(_t('Write'), 'fa-solid fa-envelope');
$tpl->content = $fbox->render();

return $tpl->response();

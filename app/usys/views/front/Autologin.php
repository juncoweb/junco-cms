<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->title(_t('Sorry! failed login.'), ['document_title' => _t('Log in')]);
$tpl->content = _t('The code used is invalid or has expired.');

return $tpl->response();

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$html = _t('The code used is invalid or has expired.');

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->title(_t('Sorry! failed login.'), ['document_title' => _t('Log in')]);
$tpl->content($html);

return $tpl->response();

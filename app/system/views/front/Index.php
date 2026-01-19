<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$html = '<div class="italic">' . _t('This site will soon be available.') . '</div>';

// template
$tpl = Template::get();
$tpl->options();
$tpl->title(_t('Site under construction'));
$tpl->content($html);

return $tpl->response();

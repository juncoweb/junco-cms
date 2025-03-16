<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// template
$tpl = Template::get();
$tpl->options();
$tpl->title(_t('Site under construction'));
$tpl->content = '<div class="italic">' . _t('This site will soon be available.') . '</div>';

return $tpl->response();

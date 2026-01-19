<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// vars
$html = snippet('contact', $snippet)->render();

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->title(_t('Contact'));
$tpl->content($html);

return $tpl->response();

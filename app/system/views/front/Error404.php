<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

$html = '<div class="m-8 italic">'
	. '<p>The requested was not found on this server. That’s all we know.</p>'
	. '<p>If you want, you can search the site.</p>'
	. '</div>';

// template
$tpl = Template::get();
//$tpl->options();
$tpl->title(_t('404. That’s an error.'));
$tpl->content = $html;

return $tpl->response();

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// template
$tpl = Template::get('install');
$tpl->options(['hash' => 'license']);
$tpl->title(_t('License'));
$tpl->content('<div class="panel"><div class="panel-body">' . $license . '</div></div>');

return $tpl->response();

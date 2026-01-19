<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// template
$tpl = Template::get('install');
$tpl->options([
    'hash' => 'extensions',
    'submit' => true
]);
$tpl->title(_t('Extensions installer'));
$tpl->content(_t('Click next to install the extensions.'));

return $tpl->response();

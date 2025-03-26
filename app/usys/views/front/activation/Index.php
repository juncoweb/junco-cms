<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

if ($error) {
    $title = _t('Failed Activation!');
    $html = $error_msg;
} else {
    $title = _t('Activation Successful!');
    $html = '<p>' . _t('Your account has been successfully activated. You can now access the site with your user name and password.') . '</p>'
        . '<p>' . sprintf(_t('Sincerely %s'), config('site.name')) . '</p>';
}

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->title($title, _t('Activate account'));
$tpl->content = $html;

return $tpl->response();

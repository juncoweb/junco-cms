<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$html = '<div class="dialog dialog-success">' . _t('The message has been sent successfully.') . '</div>';

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->title(_t('Contact'));
$tpl->content($html);

return $tpl->response();

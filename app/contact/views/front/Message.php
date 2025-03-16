<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->title(_t('Contact'));
$tpl->content = '<div class="dialog dialog-success">' . _t('The message has been sent successfully.') . '</div>';

return $tpl->response();

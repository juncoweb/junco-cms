<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->title($title);
$tpl->content = '<p class="mt-4">' . $message . '</p>'
    . '<p class="mt-4 mb-8">' . sprintf(_t('Sincerely %s'), config('site.name')) . '</p>'
    . (!empty($attention) ? '<div class="dialog dialog-warning"><b>' . _t('Attention!') . '</b> ' . $attention . '</div>' : '');

return $tpl->response();

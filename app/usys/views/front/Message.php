<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$html = '<p class="mt-4">' . $message . '</p>';
$html .= '<p class="mt-4 mb-8">' . sprintf(_t('Sincerely %s'), config('site.name')) . '</p>';

if (!empty($attention)) {
    $html .= '<div class="dialog dialog-warning"><b>' . _t('Attention!') . '</b> ' . $attention . '</div>';
}

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->title($title);
$tpl->content($html);

return $tpl->response();

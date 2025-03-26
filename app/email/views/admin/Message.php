<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

//
foreach ($layouts as $i => $row) {
    $layouts[$i] = '<a href="' . $row['url'] . '" class="btn btn-small' . ($layout == $i ? ' btn-primary' : '') . '">' . $row['caption'] . '</a>';
}

$html = '<p>'
    . '<div class="btn-group"><a href="' . url('admin/email') . '" class="btn btn-small"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> ' . _t('Back') . '</a></div>'
    . '<div class="btn-group">' . implode(' · ', $layouts) . '</div>'
    . '</p>';

if ($message) {
    // tabs
    $tabs = Tabs::get();
    $tabs->tab(_t('View'), '<iframe src="' . url('admin/email/iframe') . '" style="width: 100%; min-height: 1200px; border: 0;">' . $message[0] . '</iframe>');
    $tabs->tab(_t('Html'), '<textarea class="input-field" style="min-height: 1200px;">' . htmlentities($message[0]) . '</textarea>');
    $tabs->tab(_t('Plain'), '<textarea class="input-field" style="min-height: 1200px;">' . htmlentities($message[1]) . '</textarea>');
    $html .= $tabs->render();
}

// template
$tpl = Template::get();
$tpl->editor();
$tpl->domready("JsTabs('#tabs').select()");
$tpl->title('Email Message');
$tpl->content = '<div style="max-width: 1000px;">' . $html . '</div>';

return $tpl->response();

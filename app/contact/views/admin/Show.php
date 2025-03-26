<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

$contact_email = sprintf('<a href="mailto:%s">%s</a>', $contact_email, $contact_email);

if ($user_id) {
    $contact_name .= sprintf(
        '<span class="ml-8"><i class="fa-solid fa-user"></i> <a href="%s" target="_blank" title="%s">%s</a></span>',
        $user_url,
        _t('User'),
        $fullname
    );
}

$html = '<div class="table-responsive">'
    . '<table class="table table-bordered"><tbody>'
    . '<tr>'
    .    '<th>' . _t('Name') . '</th><td>' . $contact_name . '</td>'
    . '</tr>'
    . '<tr>'
    .    '<th>' . _t('Created') . '</th>'
    .    '<td>'
    .        $created_at->format('Y-M-d')
    .        ' <span class="color-light">' . $created_at->format('H:i') . '</span>'
    .        '<span class="ml-8 color-light" title="Ip">(' . $user_ip . ')</span>'
    .    '</td>'
    . '</tr>'
    . '<tr><th>' . _t('Email') . '</th><td>' . $contact_email . '</td></tr>'
    . '<tr><th>' . _t('Message') . '</th><td>' . $contact_message . '</td></tr>'
    . '</tbody></table></div>';

// modal
$modal = Modal::get();
$modal->close();
$modal->title(_t('Contact'));
$modal->content = $html;

return $modal->response();

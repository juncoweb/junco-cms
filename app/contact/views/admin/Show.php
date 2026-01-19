<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$zoom = Zoom::get();
if ($user_id) {
    $zoom->columns(
        $zoom->group($contact_name)->setLabel(_t('Name')),
        $zoom->group($fullname)
            ->setLabel(_t('User'))
            ->setLink($user_url)
    );
} else {
    $zoom->group($contact_name)->setLabel(_t('Name'));
}
$zoom->columns(
    $zoom->date($created_at)->setLabel(_t('Created')),
    $zoom->group($user_ip)->setLabel(_t('Ip'))
);
$zoom->group(sprintf('<a href="mailto:%s">%s</a>', $contact_email, $contact_email))->setLabel(_t('Email'));
$zoom->group($contact_message)->setLabel(_t('Message'));

// modal
$modal = Modal::get();
$modal->close();
$modal->title(_t('Contact'));
$modal->content($zoom->render());

return $modal->response();

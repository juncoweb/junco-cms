<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$html = '<p class="text-right">'
    . '<a href="' . url('my/notifications') . '">' . _t('View all') . ($num_notifications ? ' (+' . $num_notifications . ')' : '') . '</a>'
    . '</p>';

if ($rows) {
    foreach ($rows as $row) {
        if ($row['url']) {
            $row['notification_message'] = '<div class="flex">'
                . '<div class="flex-auto">' . $row['notification_message'] . '</div>'
                . '<div class="text-center"><a href="' . $row['url'] . '"><i class="fa-solid fa-chevron-right color-default font-large"></i></a></div>'
                . '</div>';
        }

        $html .= '<div class="dialog">' . $row['notification_message'] . '</div>';
    }
} else {
    $html .= '<div class="dialog dialog-waring">' . _t('No notifications.') . '</div>';
}

return '<div class="notifications">'
    . '<h3>' . _t('Notifications') . '</h3>'
    . $html
    . '</div>';

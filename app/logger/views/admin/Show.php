<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

$_context = '';
if ($context) {
    $session = session();
    foreach ($context as $name => $value) {
        if ($name == 'user_agent') {
            $browser = $session->getBrowser($value);
            $_context .= '<div class="badge badge-regular" title="' . $value . '">' . $name . ': ' . implode('/', $browser) . '</div>';
        } else {
            $_context .= '<div class="badge badge-regular">' . $name . ': ' . $value . '</div>';
        }
    }
}

$zoom = Zoom::get();
$zoom->group('<span class="text-uppercase">' . $level . '</span>')->setLabel(_t('Level'));
$zoom->group($message)->setLabel(_t('Message'));
$zoom->group($file ?: '-')->setLabel(_t('File'));
$zoom->date($created_at)->setLabel(_t('Created'));
$zoom->group($_context ?: '-')->setLabel(_t('Context'));
$zoom->group($backtrace ? '<div>' . implode('</div><div>', $backtrace) . '</div>' : '-')->setLabel(_t('Trace'));

// modal
$modal = Modal::get();
$modal->close();
$modal->title(_t('Info'));
$modal->content = $zoom->render();

return $modal->response();

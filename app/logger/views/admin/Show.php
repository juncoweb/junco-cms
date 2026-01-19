<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */


if ($context) {
    foreach ($context as $name => $value) {
        $context[$name] = '<div class="badge badge-regular">' . $name . ': ' . $value . '</div>';
    }
    $context = implode($context);
}

$zoom = Zoom::get();
$zoom->group($level)->setLabel(_t('Level'));
$zoom->group($message)->setLabel(_t('Message'));
$zoom->group($file ?: '-')->setLabel(_t('File'));
$zoom->date($created_at)->setLabel(_t('Created'));
$zoom->group($context ?: '-')->setLabel(_t('Context'));
$zoom->group($backtrace ? '<div>' . implode('</div><div>', $backtrace) . '</div>' : '-')->setLabel(_t('Trace'));

// modal
$modal = Modal::get();
$modal->close();
$modal->title(_t('Info'), 'fa-solid fa-circle-info');
$modal->content($zoom->render());

return $modal->response();

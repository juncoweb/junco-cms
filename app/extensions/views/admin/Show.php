<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$zoom = Zoom::get();
$zoom->columns(
    $zoom->group($developer_name)->setLabel(_t('Developer'))->setLink($project_url),
    $zoom->group($extension_version)->setLabel(_t('Version'))
);
$zoom->columns(
    $zoom->group($extension_credits)->setLabel(_t('Credits')),
    $zoom->group($extension_license)->setLabel(_t('License'))
);
$zoom->columns(
    $zoom->date($created_at)->setLabel(_t('Created')),
    $zoom->date($updated_at)->setLabel(_t('Updated'))
);
//$zoom->group($package_id)->setLabel(_t('Package'));
//$zoom->group($extension_alias)->setLabel(_t('alias'));
//$zoom->group($extension_key)->setLabel(_t('key'));
//$zoom->status($status['title'], $status['color']);
if ($extension_abstract) {
    $zoom->group($extension_abstract)->setLabel(_t('Abstract'));
}

$html = $zoom->render();

if ($developer_mode) {
    $components = $components
        ? implode(array_map(
            fn($component) => '<div title="' . $component['title'] . '" class="badge badge-primary text-uppercase">' . $component['caption'] . '</div>',
            $components
        ))
        : '-';

    $zoom = Zoom::get();
    $zoom->group($extension_require)->setLabel(_t('Require'));
    $zoom->group($components)->setLabel(_t('Components'));
    $zoom->group($db_queries ?: '-')->setLabel(_t('Queries'));
    $zoom->group($xdata ?: '-')->setLabel(_t('Data'));
    $html .= '<h3 class="mt-4">' . _t('Package') . '</h3>';
    $html .= $zoom->render();
}


// modal
$modal = Modal::get();
$modal->close();
$modal->title($extension_name, 'fa-solid fa-puzzle-piece');
$modal->content($html);

return $modal->response();

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Extensions\XData\MalformedDataException;
use Junco\Extensions\XData\XData;
use Junco\Menus\XStorage;

return function (?XData $xdata = null, $cdata = null) {
    if ($xdata) {
        $data            = $xdata->getData();
        $extension_id    = $xdata->extension_id;
        $extension_alias = $xdata->extension_alias;
    } else {
        $data            = $cdata['data'];
        $extension_id    = $cdata['extension_id'];
        $extension_alias = $cdata['extension_alias'];
    }

    $translate  = [];
    $rows       = [];

    foreach ($data as $r) {
        $row = [
            'menu_key'          => (string)$r['menu_key'],
            'menu_default_path' => (string)$r['menu_path'],
            'menu_order'        => (int)$r['menu_order'],
            'menu_url'          => (string)$r['menu_url'],
            'menu_image'        => (string)$r['menu_image'],
            'menu_hash'         => (string)$r['menu_hash'],
            'menu_params'       => (string)$r['menu_params'],
            'status'            => (bool)$r['status'],
            'is_distributed'    => 1,
        ];

        // validate
        if (!$row['menu_key']) {
            throw new MalformedDataException();
        }

        if (!$row['menu_default_path']) {
            throw new MalformedDataException();
        }

        $rows[]      = $row;
        $path        = explode('|', $row['menu_default_path']);
        $translate[] = array_pop($path);
    }

    (new XStorage)->import($extension_id, $rows, $cdata === null);
    (new LanguageHelper)->translate('menus.' . $extension_alias, $translate);
};

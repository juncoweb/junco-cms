<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Extensions\XData\MalformedDataException;

/**
 * Import
 *
 * @param object $xdata
 *
 * @return void
 */
return function (&$xdata = null, $cdata = null) {
    // vars
    if ($xdata) {
        $data            = $xdata->getData();
        $extension_id    = $xdata->extension_id;
        $extension_alias = $xdata->extension_alias;
    } else {
        $data            = $cdata['data'];
        $extension_id    = $cdata['extension_id'];
        $extension_alias = $cdata['extension_alias'];
    }

    $has        = [];
    $inserts    = [];
    $updates    = [];
    $menu_id    = [];
    $translate  = [];
    $db         = db();

    // set
    $has = $db->query("
	SELECT CONCAT(menu_key, '||', menu_default_path), id
	FROM `#__menus`
	WHERE extension_id = $extension_id
	AND is_distributed = 1")->fetchAll(Database::FETCH_COLUMN, [0 => 1]);

    foreach ($data as $r) {
        $row = [
            'menu_key'            => (string)$r['menu_key'],
            'menu_default_path'    => (string)$r['menu_path'],
            'menu_order'        => (int)$r['menu_order'],
            'menu_url'            => (string)$r['menu_url'],
            'menu_image'        => (string)$r['menu_image'],
            'menu_hash'            => (string)$r['menu_hash'],
            'menu_params'        => (string)$r['menu_params'],
            'is_distributed'    => 1,
        ];
        $key = $row['menu_key'] . '||' . $row['menu_default_path'];

        // validate
        if (!$row['menu_key']) {
            throw new MalformedDataException();
        }
        if (!$row['menu_default_path']) {
            throw new MalformedDataException();
        }

        if (isset($has[$key])) {
            $updates[] = $row;
            $menu_id[] = $has[$key];
            unset($has[$key]);
        } else {
            $row['extension_id'] = $extension_id;
            $row['menu_path']    = $row['menu_default_path'];
            $row['status']       = $r['status'] ? 1 : 0;
            $inserts[]           = $row;
        }

        $path = explode('|', $row['menu_default_path']);
        $translate[] = array_pop($path);
    }

    if ($updates) {
        $db->execAll("UPDATE `#__menus` SET ?? WHERE id = ?", $updates, $menu_id);
    }
    if ($inserts) {
        $db->execAll("INSERT INTO `#__menus` (??) VALUES (??)", $inserts);
    }
    if ($cdata === null && $has) {
        $db->exec("DELETE FROM `#__menus` WHERE id IN (?..)", array_values($has));
    }

    (new LanguageHelper())->translate('menus.' . $extension_alias, $translate);
};

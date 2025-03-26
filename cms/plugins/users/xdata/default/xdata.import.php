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
return function (&$xdata) {
    // vars
    $data        = $xdata->getData();
    $db            = db();
    $insert        = [];
    $update        = [];
    $label_id    = [];

    // set
    $current = $db->safeFind("
	SELECT
	 CONCAT(extension_id, '-', label_key),
	 id
	FROM `#__users_roles_labels`
	WHERE extension_id = ?", $xdata->extension_id)->fetchAll(Database::FETCH_COLUMN, [0 => 1]);

    foreach ($data as $r) {
        $row = [
            'extension_id'        => $xdata->extension_id,
            'label_key'            => $r['label_key'] ?? '',
            'label_name'        => $r['label_name'] ?? '',
            'label_description'    => $r['label_description'] ?? ''
        ];

        // validate
        if (
            $row['label_key']
            && preg_match('/[^a-z0-9_]/', $row['label_key'])
        ) {
            throw new MalformedDataException();
        }

        $index = $row['extension_id'] . '-' . $row['label_key'];

        if (isset($current[$index])) {
            $update[] = $row;
            $label_id[] = $current[$index];
        } else {
            $insert[] = $row;
        }
    }

    if ($update) {
        $db->safeExecAll("UPDATE `#__users_roles_labels` SET ?? WHERE id = ?", $update, $label_id);
    }
    if ($insert) {
        $db->safeExecAll("INSERT INTO `#__users_roles_labels` (??) VALUES (??)", $insert);
    }

    $delete = array_values(array_diff($current, $label_id));

    if ($delete) {
        $db->safeExec("DELETE FROM `#__users_roles_labels_map` WHERE label_id IN (?..)", $delete);
        $db->safeExec("DELETE FROM `#__users_roles_labels` WHERE id IN (?..)", $delete);
    }
};

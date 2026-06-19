<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users\Repository;

use Database;

class XStorage
{
    // vars
    protected Database $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Has
     * 
     * @param int $extension_id
     * 
     * @return bool
     */
    public function has(int $extension_id): bool
    {
        return (bool)$this->db->query("
        SELECT COUNT(*)
        FROM `#__users_roles_labels`
        WHERE extension_id = ?", $extension_id)->fetchColumn();
    }

    /**
     * Fetch all
     * 
     * @param int $extension_id
     * 
     * @return array
     */
    public function fetchAll(int $extension_id): array
    {
        return $this->db->query("
        SELECT
         label_key ,
         label_name ,
         label_description
        FROM `#__users_roles_labels`
        WHERE extension_id = ?", $extension_id)->fetchAll();
    }

    /**
     * Import
     * 
     * @param array $rows
     * @param int   $extension_id
     * 
     * @return bool
     */
    public function import(array $rows, int $extension_id): bool
    {
        $insert   = [];
        $update   = [];
        $label_id = [];
        $current  = $this->getCurLabels($extension_id);

        foreach ($rows as $r) {
            $row = [
                'extension_id'      => $extension_id,
                'label_key'         => $r['label_key'] ?? '',
                'label_name'        => $r['label_name'] ?? '',
                'label_description' => $r['label_description'] ?? ''
            ];

            if (!$this->isValidKey($row['label_key'])) {
                return false;
            }

            $index      = $row['extension_id'] . '-' . $row['label_key'];
            $__label_id = $current[$index] ?? 0;

            if ($__label_id) {
                $label_id[] = $__label_id;
                $update[] = $row;
            } else {
                $insert[] = $row;
            }
        }

        if ($update) {
            $this->db->execAll("UPDATE `#__users_roles_labels` SET ?? WHERE id = ?", $update, $label_id);
        }
        if ($insert) {
            $this->db->execAll("INSERT INTO `#__users_roles_labels` (??) VALUES (??)", $insert);
        }

        $delete = array_values(array_diff($current, $label_id));

        if ($delete) {
            $this->db->exec("DELETE FROM `#__users_roles_labels_map` WHERE label_id IN (?..)", $delete);
            $this->db->exec("DELETE FROM `#__users_roles_labels` WHERE id IN (?..)", $delete);
        }

        return true;
    }

    /**
     * Delete
     * 
     * @param int $extension_id
     * 
     * @return void
     */
    public function delete(int $extension_id): void
    {
        $this->db->exec("DELETE FROM `#__users_roles_labels_map` WHERE label_id IN (
            SELECT id
            FROM `#__users_roles_labels`
            WHERE extension_id = ?
        )", $extension_id);

        $this->db->exec("DELETE FROM `#__users_roles_labels` WHERE extension_id = ?", $extension_id);
    }

    /**
     * Get
     * 
     * @param int $extension_id
     * 
     * @return array
     */
    protected function getCurLabels(int $extension_id): array
    {
        return $this->db->query("
        SELECT
         CONCAT(extension_id, '-', label_key),
         id
        FROM `#__users_roles_labels`
        WHERE extension_id = ?", $extension_id)->fetchAll(Database::FETCH_COLUMN, [0 => 1]);
    }

    /**
     * Get
     * 
     * @param string $label_key
     * 
     * @return bool
     */
    protected function isValidKey(string $label_key): bool
    {
        return !$label_key || !preg_match('/[^a-z0-9_]/', $label_key);
    }
}

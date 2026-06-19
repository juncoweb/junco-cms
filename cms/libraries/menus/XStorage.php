<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Menus;

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
        FROM `#__menus`
        WHERE extension_id = ?
        AND is_distributed = 1", $extension_id)->fetchColumn();
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
         menu_key ,
         menu_default_path AS menu_path ,
         menu_order ,
         menu_url ,
         menu_image ,
         menu_hash ,
         menu_params ,
         status
        FROM `#__menus`
        WHERE extension_id = ?
        AND is_distributed = 1", $extension_id)->fetchAll();
    }

    /**
     * Import
     * 
     * @param int   $extension_id
     * @param array $rows
     * @param bool  $clean
     * 
     * @return void
     */
    public function import(int $extension_id, array $rows, bool $clean = false): void
    {
        $inserts = [];
        $updates = [];
        $menu_id = [];
        $has     = $this->getKeys($extension_id);

        foreach ($rows as $row) {
            $key = $row['menu_key'] . '||' . $row['menu_default_path'];

            if (isset($has[$key])) {
                $id = $has[$key];

                unset($has[$key]);
                unset($row['status']);

                $updates[] = $row;
                $menu_id[] = $id;
            } else {
                $row['extension_id'] = $extension_id;
                $row['menu_path']    = $row['menu_default_path'];
                $row['status']       = $row['status'] ? 1 : 0;
                $inserts[]           = $row;
            }
        }

        if ($updates) {
            $this->db->execAll("UPDATE `#__menus` SET ?? WHERE id = ?", $updates, $menu_id);
        }

        if ($inserts) {
            $this->db->execAll("INSERT INTO `#__menus` (??) VALUES (??)", $inserts);
        }

        if ($clean && $has) {
            $this->db->exec("DELETE FROM `#__menus` WHERE id IN (?..)", array_values($has));
        }
    }

    /**
     * Delete
     * 
     * @param int $extension_id
     * 
     * @return int
     */
    public function delete(int $extension_id): int
    {
        return $this->db->exec("DELETE FROM `#__menus` WHERE extension_id = ?", $extension_id);;
    }

    /**
     * Get
     * 
     * @param int $extension_id
     * 
     * @return array
     */
    protected function getKeys(int $extension_id): array
    {
        return $this->db->query("
        SELECT CONCAT(menu_key, '||', menu_default_path), id
        FROM `#__menus`
        WHERE extension_id = ?
        AND is_distributed = 1", $extension_id)->fetchAll(Database::FETCH_COLUMN, [0 => 1]);
    }
}

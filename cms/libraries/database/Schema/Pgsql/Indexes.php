<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql;

use Junco\Database\Schema\Interface\IndexesInterface;
use Database;

class Indexes implements IndexesInterface
{
    // use
    use IndexesTrait;

    //
    protected $db;

    /**
     * Constructor
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Show Indexes
     * 
     * @param string $TableName
     * @param array  $where
     * 
     * @return array
     */
    public function fetchAll(string $TableName, array $where = []): array
    {
        if ($where) {
            foreach ($where as $field => $value) {
                if (is_string($value)) {
                    $this->db->where("`$field` = ?", $value);
                } else {
                    $this->db->where("`$field` IN ( ?.. )", $value);
                }
            }
        }

        return $this->db->query("SHOW INDEX FROM `$TableName` [WHERE]")->fetchAll();
    }

    /**
     * Add Index
     * 
     * @param string $TableName
     * @param string $IndexName
     * @param array  $Index
     * 
     * @return int
     */
    public function create(string $TableName, string $IndexName, array $Index): int
    {
        $Columns = $this->getIndexColumnsStatement($Index['Columns']);
        $has = $this->db->query("SHOW INDEX FROM `$TableName` WHERE Key_name = '$IndexName'")->fetchColumn();

        if ($IndexName == 'PRIMARY') {
            if ($has) {
                return $this->db->exec("ALTER TABLE `$TableName` DROP PRIMARY KEY, ADD PRIMARY KEY ($Columns)");
            }
            return $this->db->exec("ALTER TABLE `$TableName` ADD PRIMARY KEY ($Columns)");
        }
        if ($has) {
            return $this->db->exec("ALTER TABLE `$TableName` DROP INDEX `$IndexName`, ADD $Index[Type] `$IndexName` ($Columns)");
        }
        return $this->db->exec("ALTER TABLE `$TableName` ADD $Index[Type] `$IndexName` ($Columns)");
    }

    /**
     * Drop Index
     * 
     * @param string $TableName
     * @param string $IndexName
     * @param array  $Index
     * 
     * @return int
     */
    public function drop(string $TableName, string $IndexName, array $Index = []): int
    {
        if ($IndexName == 'PRIMARY') {
            return $this->db->exec("ALTER TABLE `$TableName` DROP PRIMARY KEY");
        }

        return $this->db->exec("ALTER TABLE `$TableName` DROP INDEX `$IndexName`");
    }
}

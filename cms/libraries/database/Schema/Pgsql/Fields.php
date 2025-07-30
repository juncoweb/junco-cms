<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql;

use Junco\Database\Schema\Interface\FieldsInterface;
use Database;

class Fields implements FieldsInterface
{
    // use
    use FieldsTrait;

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
     * Show Fields
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
                if ($field == 'CollationIsNot') {
                    $this->db->where("Collation IS NOT NULL");
                    $this->db->where("Collation <> ?", $value);
                } elseif (is_string($value)) {
                    $this->db->where("`$field` = ?", $value);
                } else {
                    $this->db->where("`$field` IN ( ?.. )", $value);
                }
            }
        }

        return $this->db->query("SHOW FULL COLUMNS FROM `$TableName` [WHERE]")->fetchAll();
    }

    /**
     * Get Fields
     * 
     * @param string $TableName
     * @param array  $where
     * 
     * @return array
     */
    public function list(string $TableName, array $where = []): array
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

        return $this->db->query("DESCRIBE `$TableName` [WHERE]")->fetchAll(\Database::FETCH_COLUMN, [0 => 0]);
    }

    /**
     * Alter Fields
     * 
     * @param string  $TableName
     * @param array	  $Fields
     * 
     * @return int
     */
    public function alter(string $TableName, array $Fields): int
    {
        $sql = [];
        foreach ($Fields as $FieldName => $Field) {
            if (!empty($Field['ChangeField'])) {
                $head = "CHANGE `$Field[ChangeField]` `$FieldName`";
            } elseif (!empty($Field['ModifyField'])) {
                $head = "MODIFY `$FieldName`";
            } else {
                $head = "ADD `$FieldName`";
            }

            $sql[] = $head . ' ' . $this->getFieldStatement($Field);
        }
        $sql = implode(', ', $sql);
        //throw new Exception($sql);
        return $this->db->exec("ALTER TABLE `$TableName` $sql");
    }

    /**
     * Drop Fields
     * 
     * @param string		$TableName
     * @param string|array	$FieldNames
     * 
     * @return int
     */
    public function drop(string $TableName, string|array $FieldNames): int
    {
        if (is_array($FieldNames)) {
            $FieldNames = implode('`, DROP COLUMN `', $FieldNames);
        }

        return $this->db->exec("ALTER TABLE `$TableName` DROP COLUMN `$FieldNames`");
    }
}

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Sqlite;

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
        $Fields = $this->db->query("PRAGMA table_info($TableName)")->fetchAll();
        $list = [];

        foreach ($Fields as $Field) {
            $Field = [
                'Field'      => $Field['name'],
                'Type'       => $Field['type'],
                'Collation'  => null,
                'Null'       => $Field['notnull'] ? 'NO' : 'YES',
                'Key'        => $Field['pk'] ? 'PRI' : null,

                'Default'    => $Field['name'],
                'Extra'      => $Field['name'],
                'Privileges' => null,
                'Comment'    => '',
                //
                'pk'         => $Field['pk'],
            ];

            if ($where && !$this->passThru($Field, $where)) {
                continue;
            }

            $list[] = $Field;
        }

        return $list;
    }

    /**
     * @return bool
     */
    protected function passThru(array $Field, array $where): bool
    {
        foreach ($where as $field_name => $value) {
            if ($field_name == 'CollationIsNot') {
                if ($Field['Collation'] === null || $Field['Collation'] == $value) {
                    return false;
                }
            } elseif (is_array($field_name)) {
                if (!in_array($value, $field_name)) {
                    return false;
                }
            } elseif ($Field[$field_name] != $value) {
                return false;
            }
        }

        return true;
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
        $rows = $this->fetchAll($TableName, $where);
        $list = [];

        foreach ($rows as $row) {
            $list[$row['Field']] = $row['Field'];
        }

        return $list;
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
        /* $sql = [];
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
        return $this->db->exec("ALTER TABLE `$TableName` $sql"); */
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
        if (!is_array($FieldNames)) {
            $FieldNames = [$FieldNames];
        }

        $sql = '';
        foreach ($FieldNames as $FieldName) {
            $sql .= "ALTER TABLE $TableName DROP COLUMN $FieldName;";
        }

        return $this->db->exec($sql);
    }
}

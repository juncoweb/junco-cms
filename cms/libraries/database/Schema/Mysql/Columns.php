<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql;

use Junco\Database\Schema\Interface\ColumnsInterface;
use Junco\Database\Schema\Interface\Entity\ColumnInterface;
use Junco\Database\Schema\Mysql\Entity\Column;
use Database;

class Columns implements ColumnsInterface
{
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
     * Fetch all
     * 
     * @param string $TableName
     * @param array  $where
     * 
     * @return ColumnInterface[]
     */
    public function fetchAll(string $TableName, array $where = []): array
    {
        if ($where) {
            foreach ($where as $column => $value) {
                if ($column == 'Name') {
                    $column = 'Field';
                }

                if ($column == 'CollationIsNot') {
                    $this->db->where("Collation IS NOT NULL");
                    $this->db->where("Collation <> ?", $value);
                } elseif (is_string($value)) {
                    $this->db->where("`$column` = ?", $value);
                } else {
                    $this->db->where("`$column` IN ( ?.. )", $value);
                }
            }
        }

        $columns = $this->db->query("SHOW FULL COLUMNS FROM `$TableName` [WHERE]")->fetchAll();

        return array_map(
            fn($column) => new Column(
                $TableName,
                $column['Field'],
                $column['Type'],
                $column['Collation'],
                $column['Null'] == 'YES',
                $column['Key'],
                $column['Default'],
                $column['Extra'],
                $column['Comment']
            ),
            $columns
        );
    }

    /**
     * Fetch
     * 
     * @param string $TableName
     * @param string $ColumnName
     * 
     * @return ?ColumnInterface
     */
    public function fetch(string $TableName, string $ColumnName): ?ColumnInterface
    {
        return $this->fetchAll($TableName, ['Field' => $ColumnName])[0] ?? null;
    }

    /**
     * Get Columns
     * 
     * @param string $TableName
     * @param array  $where
     * 
     * @return array
     */
    public function list(string $TableName, array $where = []): array
    {
        if ($where) {
            foreach ($where as $column => $value) {
                if (is_string($value)) {
                    $this->db->where("`$column` = ?", $value);
                } else {
                    $this->db->where("`$column` IN ( ?.. )", $value);
                }
            }
        }
        return $this->db->query("
        SHOW COLUMNS
        FROM `$TableName`
        [WHERE]")->fetchAll(Database::FETCH_COLUMN, ['Field' => 'Field']);
    }

    /**
     * Alter
     * 
     * @param ColumnInterface $column
     * 
     * @return int
     */
    public function alter(ColumnInterface $column): int
    {
        return $this->db->exec(
            $column->getAlterStatement()
        );
    }

    /**
     * Drop Columns
     * 
     * @param string	   $TableName
     * @param string|array $ColumnNames
     * 
     * @return int
     */
    public function drop(string $TableName, string|array $ColumnNames): int
    {
        if (is_array($ColumnNames)) {
            $ColumnNames = implode('`, DROP COLUMN `', $ColumnNames);
        }

        return $this->db->exec("ALTER TABLE `$TableName` DROP COLUMN `$ColumnNames`");
    }
}

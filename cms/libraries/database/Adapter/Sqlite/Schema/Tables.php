<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter\Sqlite\Schema;

use Junco\Database\Base\Schema\TablesInterface;
use Junco\Database\Base\Entity\TableInterface;
use Junco\Database\Adapter\Sqlite\Entity\Column;
use Junco\Database\Adapter\Sqlite\Entity\ForeignKey;
use Junco\Database\Adapter\Sqlite\Entity\Index;
use Junco\Database\Adapter\Sqlite\Entity\IndexColumn;
use Junco\Database\Adapter\Sqlite\Entity\Table;
use Database;

class Tables implements TablesInterface
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
     * Has
     * 
     * @param string $TableName
     * 
     * @return bool
     */
    public function has(string $TableName): bool
    {
        return (bool)$this->db->query("
        SELECT name
        FROM sqlite_master
        WHERE type = 'table'
        AND name = ?", $TableName)->fetch();
    }

    /**
     * Get all tables
     * 
     * @return array An associative array of all tables.
     */
    public function list(): array
    {
        return $this->db->query("
        SELECT name
        FROM sqlite_master
        WHERE type = 'table'
        AND name NOT LIKE 'sqlite_%'
        ORDER BY name")->fetchAll(Database::FETCH_COLUMN, [0 => 0]);
    }

    /**
     * Show tables
     * 
     * @param array $where
     * 
     * @return Table[]
     */
    public function fetchAll(array $where = []): array
    {
        $this->db->where("type = 'table'");
        $this->db->where("name NOT LIKE 'sqlite_%'");

        if ($where) {
            foreach ($where as $column => $value) {
                if ($column === 'Search') {
                    $this->db->where("name LIKE %?", $value);
                } else {
                    $column = $this->getColumnName($column);

                    if (!$column) {
                        return [];
                    }

                    if (is_string($value)) {
                        $this->db->where("$column = ?", $value);
                    } else {
                        $this->db->where("$column IN ( ?.. )", $value);
                    }
                }
            }
        }

        $tables = $this->db->query("
        SELECT
         name
        FROM sqlite_master
        [WHERE]
        ORDER BY name")->fetchAll();

        return array_map(fn($table) => new Table(
            $table['name'],
            null,
            null,
            null,
            0,
            0, // ????????????????
        ), $tables);
    }

    /**
     * Fetch
     * 
     * @param string $TableName
     * 
     * @return ?TableInterface
     */
    public function fetch(string $TableName): ?TableInterface
    {
        $tables = $this->fetchAll(['Name' => $TableName]);

        return $tables[0] ?? null;
    }

    /**
     * New
     * 
     * @param string $Name
     * 
     * @return TableInterface
     */
    public function newTable(string $Name): TableInterface
    {
        return new Table($Name);
    }

    /**
     * Create
     * 
     * @param TableInterface $Table
     * 
     * @return int
     */
    public function create(TableInterface $Table): int
    {
        return $this->db->exec(
            $Table->getCreateStatement()
        );
    }

    /**
     * Alter
     * 
     * @param TableInterface $Table
     * 
     * @return int
     */
    public function alter(TableInterface $Table): int
    {
        return $this->db->exec(
            $Table->getAlterStatement()
        );
    }

    /**
     * Copy
     * 
     * @param string $FromTableName
     * @param string $ToTableName
     * @param bool   $CopyRegisters
     * 
     * @return int
     */
    public function copy(string $FromTableName, string $ToTableName, bool $CopyRegisters = false): int
    {
        return $CopyRegisters
            ? $this->db->exec("CREATE TABLE $ToTableName AS SELECT * FROM $FromTableName")
            : $this->db->exec("CREATE TABLE $ToTableName AS SELECT * FROM $FromTableName WHERE 0");
    }

    /**
     * Rename
     * 
     * @param string $FromTableName
     * @param string $ToTableName
     * 
     * @return int
     */
    public function rename(string $FromTableName, string $ToTableName): int
    {
        return $this->db->exec("ALTER TABLE $FromTableName RENAME TO $ToTableName");
    }

    /**
     * Truncate
     * 
     * @param string|array $TableNames
     * 
     * @return int
     */
    public function truncate(string|array $TableNames): int
    {
        if (!is_array($TableNames)) {
            $TableNames = [$TableNames];
        }

        $sql1 = '';
        $sql2 = '';
        foreach ($TableNames as $TableName) {
            $sql1 .= "DELETE FROM $TableName;";
            $sql2 .= "UPDATE SQLITE_SEQUENCE SET seq = 0 WHERE name = '$TableName';";
        }

        $this->db->exec($sql1);
        return $this->db->exec($sql2);
    }

    /**
     * Drop
     * 
     * @param string|array $TableNames
     * 
     * @return int
     */
    public function drop(string|array $TableNames): int
    {
        if (!is_array($TableNames)) {
            $TableNames = [$TableNames];
        }

        $sql = '';
        foreach ($TableNames as $TableName) {
            $sql .= "DROP TABLE IF EXISTS $TableName;";
        }

        return $this->db->exec($sql);
    }

    /**
     * From
     * 
     * @param array $Data
     * 
     * @return ?TableInterface
     */
    public function from(array $data): ?TableInterface
    {
        foreach ($data['Columns'] as $i => $ColumnData) {
            $data['Columns'][$i] = Column::from($data['Name'], $ColumnData);
        }

        foreach (($data['Indexes'] ??= []) as $i => $IndexData) {
            $data['Indexes'][$i] = Index::from($data['Name'], $IndexData)->setColumns(
                array_map(
                    fn(array $column_data) => IndexColumn::from($column_data),
                    $IndexData['Columns']
                )
            );
        }

        foreach (($data['ForeignKeys'] ??= []) as $i => $ForeignKeyData) {
            $data['ForeignKeys'][$i] = ForeignKey::from($data['Name'], $ForeignKeyData);
        }

        return Table::from($data)
            ->setColumns($data['Columns'])
            ->setIndexes($data['Indexes'])
            ->setForeignKeys($data['ForeignKeys']);
    }

    /**
     * Get
     * 
     * @param string $column
     * 
     * @return ?string
     */
    protected function getColumnName(string $column): ?string
    {
        return $column == 'Name'
            ? 'name'
            : null;
    }
}

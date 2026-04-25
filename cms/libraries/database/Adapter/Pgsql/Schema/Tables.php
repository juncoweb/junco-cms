<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter\Pgsql\Schema;

use Junco\Database\Adapter\Pgsql\Entity\Column;
use Junco\Database\Adapter\Pgsql\Entity\ForeignKey;
use Junco\Database\Adapter\Pgsql\Entity\Index;
use Junco\Database\Adapter\Pgsql\Entity\IndexColumn;
use Junco\Database\Adapter\Pgsql\Entity\Table;
use Junco\Database\Base\Schema\TablesInterface;
use Junco\Database\Base\Entity\TableInterface;
use Database;

class Tables implements TablesInterface
{
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
        return $this->db->query("
        SELECT TRUE
        FROM information_schema.tables
        WHERE table_name = ?", $TableName)->fetchColumn();
    }

    /**
     * Get all tables
     * 
     * @return array An associative array of all tables.
     */
    public function list(): array
    {
        return $this->db->query("
        SELECT tablename
        FROM pg_catalog.pg_tables
        WHERE schemaname != 'pg_catalog'
        AND schemaname != 'information_schema'
        ORDER BY tablename")->fetchAll(Database::FETCH_COLUMN, ['tablename' => 'tablename']);
    }

    /**
     * Show tables
     * 
     * @param array $where
     * 
     * @return TableInterface[]
     */
    public function fetchAll(array $where = []): array
    {
        if ($where) {
            foreach ($where as $column => $value) {
                if ($column === 'Search') {
                    $this->db->where("t.table_name LIKE %?", $value);
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

        $this->db->where("t.table_schema NOT IN ('pg_catalog', 'information_schema')");
        $this->db->where("t.table_type = 'BASE TABLE'");

        $tables = $this->db->query("
        SELECT
         t.table_name ,
         pg_get_serial_sequence(c.table_name, c.column_name) AS SequenceName,
         (SELECT last_value /* + increment_by  */
          FROM pg_sequences
          WHERE sequencename = CONCAT(c.table_name, '_', c.column_name, '_seq')
         ) AS auto_increment
        FROM information_schema.tables AS t
        JOIN information_schema.columns AS c ON c.table_name = t.table_name AND c.column_default LIKE 'nextval(%'
        [WHERE]
        ORDER BY t.table_name")->fetchAll();

        return array_map(fn($table) => new Table(
            $table['table_name'],
            null,
            null,
            null,
            0,
            $table['auto_increment'] ?? 1,
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
            ? $this->db->exec("CREATE TABLE $ToTableName AS TABLE $FromTableName")
            : $this->db->exec("CREATE TABLE $ToTableName AS TABLE $FromTableName WITH NO DATA");
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
        if (is_string($TableNames)) {
            $TableNames = [$TableNames];
        }

        $total = 0;
        foreach ($TableNames as $TableName) {
            $total += $this->db->exec("TRUNCATE TABLE $TableName");
        }

        return $total;
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
        if (is_array($TableNames)) {
            $TableNames = implode(', ', $TableNames);
        }

        return $this->db->exec("DROP TABLE IF EXISTS $TableNames");
    }

    /**
     * From
     * 
     * @param array $data
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
        return match ($column) {
            'Name'      => 'table_name',
            //'Engine'    => 'Engine',
            //'Collation' => 'Collation',
            //'Comment'   => 'Comment',
            default     => null,
        };
    }
}

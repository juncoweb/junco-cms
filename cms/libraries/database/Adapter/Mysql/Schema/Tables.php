<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter\Mysql\Schema;

use Junco\Database\Base\Schema\TablesInterface;
use Junco\Database\Base\Entity\TableInterface;
use Junco\Database\Adapter\Mysql\Entity\Column;
use Junco\Database\Adapter\Mysql\Entity\ForeignKey;
use Junco\Database\Adapter\Mysql\Entity\Index;
use Junco\Database\Adapter\Mysql\Entity\IndexColumn;
use Junco\Database\Adapter\Mysql\Entity\Table;
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
        return (bool)$this->db->query("SHOW TABLE STATUS WHERE Name = ?", $TableName)->fetch();
    }

    /**
     * Get all tables
     * 
     * @return array An associative array of all tables.
     */
    public function list(): array
    {
        return $this->db
            ->query("SHOW TABLES")
            ->fetchAll(Database::FETCH_COLUMN, [0 => 0]);
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
                    $this->db->where("Name LIKE %?", $value);
                } else {
                    $column = $this->getColumnName($column);

                    if (!$column) {
                        return [];
                    }

                    if (is_string($value)) {
                        $this->db->where("`$column` = ?", $value);
                    } else {
                        $this->db->where("`$column` IN ( ?.. )", $value);
                    }
                }
            }
        }

        $tables = $this->db->query("SHOW TABLE STATUS [WHERE]")->fetchAll();

        return array_map(fn($table) => new Table(
            $table['Name'],
            $table['Comment'] ?: null,
            $table['Engine'],
            $table['Collation'],
            $table['Data_length'],
            $table['Auto_increment'],
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
        // copy
        $result = $this->db->exec("CREATE TABLE `$ToTableName` LIKE `$FromTableName`");

        if ($CopyRegisters) {
            return $this->db->exec("INSERT INTO `$ToTableName` SELECT * FROM `$FromTableName`");
        }
        return $result;
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
        return $this->db->exec("RENAME TABLE `$FromTableName` TO `$ToTableName`");
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
            $total += $this->db->exec("TRUNCATE TABLE `$TableName`");
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
            $TableNames = implode("`, `", $TableNames);
        }

        return $this->db->exec("DROP TABLE IF EXISTS `$TableNames`");
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
            'Name'      => 'Name',
            'Engine'    => 'Engine',
            'Collation' => 'Collation',
            'Comment'   => 'Comment',
            default     => null,
        };
    }
}

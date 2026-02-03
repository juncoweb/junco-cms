<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql;

use Junco\Database\Schema\Interface\IndexesInterface;
use Junco\Database\Schema\Interface\Entity\IndexInterface;
use Junco\Database\Schema\Mysql\Entity\Index;
use Database;

class Indexes implements IndexesInterface
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
     * @param string $IndexName
     * 
     * @return bool
     */
    public function has(string $TableName, string $IndexName): bool
    {
        return (bool)$this->db->query("
        SHOW INDEX
        FROM `$TableName`
        WHERE Key_name = '$IndexName'")->fetchColumn();
    }

    /**
     * Fetch All
     * 
     * @param string $TableName
     * @param array  $where
     * 
     * @return IndexInterface[]
     */
    public function fetchAll(string $TableName, array $where = []): array
    {
        if ($where) {
            foreach ($where as $column => $value) {
                $column = $this->getColumnName($column);

                if (is_string($value)) {
                    $this->db->where("`$column` = ?", $value);
                } else {
                    $this->db->where("`$column` IN ( ?.. )", $value);
                }
            }
        }

        $rows = $this->db->query("SHOW INDEX FROM `$TableName` [WHERE]")->fetchAll();
        $indexes = [];

        foreach ($rows as $row) {
            if (!isset($indexes[$row['Key_name']])) {
                $indexes[$row['Key_name']] = new Index(
                    $row['Table'],
                    $row['Key_name'],
                    $row['Non_unique'] == '0',
                    $row['Null'] == 'YES',
                    $row['Comment'],
                    $row['Collation'],
                    $row['Cardinality'],
                    $row['Index_type']
                );
            }

            $indexes[$row['Key_name']]->addColumn(
                $row['Column_name'],
                $row['Seq_in_index'],
                $row['Index_comment'],
                $row['Sub_part']
            );
        }

        return array_values($indexes);
    }

    /**
     * Fetch
     * 
     * @param string $TableName
     * @param string $IndexName
     * 
     * @return ?IndexInterface
     */
    public function fetch(string $TableName, string $IndexName): ?IndexInterface
    {
        return $this->fetchAll($TableName, ['Name' => $IndexName])[0] ?? null;
    }

    /**
     * Create
     * 
     * @param IndexInterface $Index
     * 
     * @return int
     */
    public function create(IndexInterface $Index): int
    {
        $indexExists = $this->has(
            $Index->getTableName(),
            $Index->getName()
        );

        return $this->db->exec(
            $Index->getAlterStatement($indexExists)
        );
    }

    /**
     * Drop
     * 
     * @param string $TableName
     * @param string $IndexName
     * 
     * @return int
     */
    public function drop(string $TableName, string $IndexName): int
    {
        if ($IndexName == 'PRIMARY') {
            return $this->db->exec("ALTER TABLE `$TableName` DROP PRIMARY KEY");
        }

        return $this->db->exec("ALTER TABLE `$TableName` DROP INDEX `$IndexName`");
    }

    /**
     * New
     * 
     * @param string $TableName
     * @param string $Name
     * 
     * @return IndexInterface
     */
    public function newIndex(string $TableName, string $Name): Index
    {
        return new Index($TableName, $Name);
    }

    /**
     * Get
     */
    protected function getColumnName(string $column): string
    {
        return match ($column) {
            'Name' => 'Key_name',
            'Table' => 'Table',
            'Comment' => 'Comment',
            'Collation' => 'Collation',
            'Cardinality' => 'Cardinality',
            default => abort()
        };
    }
}

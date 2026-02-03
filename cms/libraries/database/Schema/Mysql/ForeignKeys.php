<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql;

use Junco\Database\Schema\Interface\ForeignKeysInterface;
use Junco\Database\Schema\Interface\Entity\ForeignKeyInterface;
use Junco\Database\Schema\Mysql\Entity\ForeignKey;
use Database;

class ForeignKeys implements ForeignKeysInterface
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
     * @param string $Name
     * 
     * @return bool
     */
    public function has(string $TableName, string $Name): bool
    {
        return (bool)$this->db->query("
		SELECT COUNT(*)
		FROM `information_schema`.`KEY_COLUMN_USAGE`
		WHERE TABLE_SCHEMA = (SELECT DATABASE())
		AND TABLE_NAME = ?
		AND CONSTRAINT_NAME = ?", $TableName, $Name)->fetchColumn();
    }

    /**
     * Foreign keys
     * 
     * @param string $TableName
     * @param array  $where
     * 
     * @return ForeignKey[]
     */
    public function fetchAll(string $TableName, array $where = []): array
    {
        $this->db->where("k.TABLE_SCHEMA = (SELECT DATABASE())");
        $this->db->where("k.TABLE_NAME = ?", $TableName);

        if ($where) {
            foreach ($where as $column => $value) {
                switch ($column) {
                    case 'Name':
                        $this->db->where("k.CONSTRAINT_NAME = ?", $value);
                        break;
                }
            }
        }
        $this->db->where("k.REFERENCED_COLUMN_NAME IS NOT NULL");

        $fks = $this->db->query("
		SELECT
		 k.TABLE_NAME,
		 k.COLUMN_NAME,
		 k.CONSTRAINT_NAME,
		 k.REFERENCED_TABLE_SCHEMA,
		 k.REFERENCED_TABLE_NAME,
		 k.REFERENCED_COLUMN_NAME,
		 c.UPDATE_RULE,
		 c.DELETE_RULE
		FROM `information_schema`.`KEY_COLUMN_USAGE` k
		LEFT JOIN `information_schema`.`REFERENTIAL_CONSTRAINTS` c ON (c.CONSTRAINT_NAME = k.CONSTRAINT_NAME) 
		[WHERE]
		ORDER BY k.TABLE_NAME, k.COLUMN_NAME")->fetchAll();

        return array_map(fn($fk) => new ForeignKey(
            $fk['CONSTRAINT_NAME'],
            $fk['TABLE_NAME'],
            $fk['COLUMN_NAME'],
            $fk['UPDATE_RULE'],
            $fk['DELETE_RULE'],
            $fk['REFERENCED_TABLE_SCHEMA'],
            $fk['REFERENCED_TABLE_NAME'],
            $fk['REFERENCED_COLUMN_NAME'],
        ), $fks);
    }

    /**
     * Fetch
     * 
     * @param string $TableName
     * @param string $Name
     * 
     * @return ?ForeignKey
     */
    public function fetch(string $TableName, string $Name): ?ForeignKey
    {
        $fk = $this->fetchAll($TableName, ['Name' => $Name]);

        return $fk[0] ?? null;
    }

    /**
     * Create
     * 
     * @param ForeignKeyInterface $ForeignKey
     * 
     * @return int
     */
    public function create(ForeignKeyInterface $ForeignKey): int
    {
        return $this->db->exec(
            $ForeignKey->getAlterStatement()
        );
    }

    /**
     * Drop
     * 
     * @param string       $TableName
     * @param string|array $Names
     * 
     * @return int
     */
    public function drop(string $TableName, string|array $Names): int
    {
        if (!is_array($Names)) {
            $Names = [$Names];
        }

        $drop_stmt = implode(', ', array_map(
            fn($Name) => "DROP FOREIGN KEY `$Name`",
            $Names
        ));

        return $this->db->exec("ALTER TABLE `$TableName` $drop_stmt");
    }

    /**
     * New
     * 
     * @param string $Name
     * @param string $TableName
     * @param string $ColumName
     * 
     * @return ForeignKeyInterface
     */
    public function newForeignKey(string $Name, string $TableName, string $ColumName): ForeignKeyInterface
    {
        return new ForeignKey($Name, $TableName, $ColumName);
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getRules(): array
    {
        return ['CASCADE', 'SET NULL', 'NO ACTION', 'RESTRICT'];
    }
}

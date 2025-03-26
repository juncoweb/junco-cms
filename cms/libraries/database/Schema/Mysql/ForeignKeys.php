<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql;

use Junco\Database\Schema\Interface\ForeignKeysInterface;
use Junco\Database\Schema\Entity\ForeignKey;
use Database;

class ForeignKeys implements ForeignKeysInterface
{
    //
    protected $db;
    protected array $rules = ['CASCADE', 'SET NULL', 'NO ACTION', 'RESTRICT'];

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
        return (bool)$this->db->safeFind("
		SELECT COUNT(*)
		FROM `information_schema`.`KEY_COLUMN_USAGE`
		WHERE TABLE_SCHEMA = (SELECT DATABASE())
		AND TABLE_NAME = ?
		AND CONSTRAINT_NAME = ?", $TableName, $Name)->fetchColumn();
    }

    /**
     * Foreign keys
     * 
     * @param array $where
     * 
     * @return array
     */
    public function fetchAll(array $where = []): array
    {
        $this->db->where("k.TABLE_SCHEMA = (SELECT DATABASE())");
        if ($where) {
            foreach ($where as $field => $value) {
                switch ($field) {
                    case 'TableName':
                        $this->db->where("k.TABLE_NAME = ?", $value);
                        break;
                    case 'Name':
                        $this->db->where("k.CONSTRAINT_NAME = ?", $value);
                        break;
                }
            }
        }
        $this->db->where("k.REFERENCED_COLUMN_NAME IS NOT NULL");

        $rows = $this->db->safeFind("
		SELECT
		 k.TABLE_NAME AS TableName,
		 k.COLUMN_NAME AS ColumnName,
		 k.CONSTRAINT_NAME AS Name,
		 k.REFERENCED_TABLE_SCHEMA AS ReferencedDbName,
		 k.REFERENCED_TABLE_NAME AS ReferencedTableName,
		 k.REFERENCED_COLUMN_NAME AS ReferencedColumnName,
		 c.UPDATE_RULE AS UpdateRule,
		 c.DELETE_RULE AS DeleteRule
		FROM `information_schema`.`KEY_COLUMN_USAGE` k
		LEFT JOIN `information_schema`.`REFERENTIAL_CONSTRAINTS` c ON (c.CONSTRAINT_NAME = k.CONSTRAINT_NAME) 
		[WHERE]
		ORDER BY k.TABLE_NAME, k.COLUMN_NAME")->fetchAll();

        /* foreach ($rows as $i => $row) {
			$rows[$i] = new ForeignKey(
				$row['TableName'],
				$row['ColumnName'],
				$row['Name'],
				$row['ReferencedDbName'],
				$row['ReferencedTableName'],
				$row['ReferencedColumnName']
			);
		} */

        return $rows;
    }
    /**
     * Show
     * 
     * @param string $TableName
     * @param string $Name
     * 
     * @return array
     */
    public function showData(string $TableName, string $Name): ?array
    {
        $data = $this->fetchAll(['TableName' => $TableName, 'Name' => $Name]);

        return $data ? $data[0] : null;
    }

    /**
     * Create
     * 
     * @param string $TableName
     * @param string $Name
     * @param string $ColumName
     * @param string $ReferencedTableName
     * @param string $ReferencedColumnName
     * @param string $DeleteRule
     * @param string $UpdateRule
     * 
     * @return int
     */
    public function create(
        string $TableName,
        string $Name,
        string $ColumName,
        string $ReferencedTableName,
        string $ReferencedColumnName,
        string $DeleteRule = 'RESTRICT',
        string $UpdateRule = 'RESTRICT'
    ): int {
        $this->sanitizeRule($DeleteRule);
        $this->sanitizeRule($UpdateRule);

        return $this->db->safeExec("ALTER TABLE `$TableName`"
            . " ADD CONSTRAINT `$Name` FOREIGN KEY (`$ColumName`)"
            . " REFERENCES `$ReferencedTableName` (`$ReferencedColumnName`)"
            . " ON DELETE $DeleteRule ON UPDATE $UpdateRule");
    }

    /**
     * Drop
     * 
     * @param string       $TableName
     * @param string|array $Name
     * 
     * @return int
     */
    public function drop(string $TableName, string|array $Name): int
    {
        $drop_stmt = is_array($Name)
            ? implode(', ', array_map(fn($Name) => "DROP FOREIGN KEY `$Name`", $Name))
            : "DROP FOREIGN KEY `$Name`";

        return $this->db->safeExec("ALTER TABLE `$TableName` $drop_stmt");
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Sanitize
     * 
     * @param string $rule
     */
    protected function sanitizeRule(string &$rule): void
    {
        if (!in_array($rule, $this->rules)) {
            $rule = 'RESTRICT';
        }
    }
}

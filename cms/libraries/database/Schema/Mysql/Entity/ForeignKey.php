<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql\Entity;

use Junco\Database\Schema\Interface\Entity\ForeignKeyInterface;

class ForeignKey extends Base implements ForeignKeyInterface
{
    /**
     * Constructor
     */
    public function __construct(
        protected string $name,
        protected string $table_name,
        protected string $column_name,
        protected string $update_rule = '',
        protected string $delete_rule = '',
        protected string $referenced_database_name = '',
        protected string $referenced_table_name = '',
        protected string $referenced_column_name = '',
    ) {}

    /**
     * Get
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getTableName(): string
    {
        return $this->table_name;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getColumnName(): string
    {
        return $this->column_name;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getDeleteRule(): string
    {
        return $this->delete_rule;
    }

    /**
     * Set
     * 
     * @param string $rule
     * 
     * @return self
     */
    public function setDeleteRule(string $rule): self
    {
        $this->delete_rule = $this->verifyRule($rule);
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getUpdateRule(): string
    {
        return $this->update_rule;
    }

    /**
     * Set
     * 
     * @param string $rule
     * 
     * @return self
     */
    public function setUpdateRule(string $rule): self
    {
        $this->update_rule = $this->verifyRule($rule);
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getReferencedDatabaseName(): string
    {
        return $this->referenced_database_name;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getReferencedTableName(): string
    {
        return $this->referenced_table_name;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getReferencedColumnName(): string
    {
        return $this->referenced_column_name;
    }

    /**
     * Set
     * 
     * @param string $table_name
     * @param string $column_name
     * 
     * @return self
     */
    public function setReferencedColumn(string $table_name, string $column_name): self
    {
        $this->referenced_table_name  = $table_name;
        $this->referenced_column_name = $column_name;
        return $this;
    }

    /**
     * Is
     * 
     * @param ForeignKeyInterface $foreign_key
     * 
     * @return bool
     */
    public function isEqual(ForeignKeyInterface $foreign_key): bool
    {
        return $this->name == $foreign_key->getName()
            && $this->table_name == $foreign_key->getTableName()
            && $this->column_name == $foreign_key->getColumnName()
            && $this->delete_rule == $foreign_key->getDeleteRule()
            && $this->update_rule == $foreign_key->getUpdateRule()
            && $this->referenced_database_name == $foreign_key->getReferencedDatabaseName()
            && $this->referenced_table_name == $foreign_key->getReferencedTableName()
            && $this->referenced_column_name == $foreign_key->getColumnName();
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getQueryStatement(): string
    {
        $sql = "CONSTRAINT `$this->name` FOREIGN KEY (`$this->column_name`)";
        $sql .= " REFERENCES `$this->referenced_table_name` (`$this->referenced_column_name`)";

        if ($this->delete_rule) {
            $sql .= " ON DELETE $this->delete_rule";
        }

        if ($this->update_rule) {
            $sql .= " ON UPDATE $this->update_rule";
        }

        return $sql;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getAlterStatement(): string
    {
        return "ALTER TABLE `$this->table_name` ADD " . $this->getQueryStatement();
    }

    /**
     * To array
     * 
     * @return string
     */
    public function toArray(): array
    {
        return [
            'Name'                   => $this->name,
            'ColumnName'             => $this->column_name,
            'UpdateRule'             => $this->update_rule,
            'DeleteRule'             => $this->delete_rule,
            'ReferencedDatabaseName' => $this->referenced_database_name,
            'ReferencedTableName'    => $this->referenced_table_name,
            'ReferencedColumnName'   => $this->referenced_column_name
        ];
    }

    /**
     * From
     * 
     * @param string $table_name
     * @param array  $data
     * 
     * @return ?self
     */
    public static function from(string $table_name, array $data): ?self
    {
        return new self(
            $data['Name'],
            $table_name,
            $data['ColumnName'],
            $data['UpdateRule'],
            $data['DeleteRule'],
            $data['ReferencedDatabaseName'],
            $data['ReferencedTableName'],
            $data['ReferencedColumnName']
        );
    }

    /**
     * Verify
     */
    protected function verifyRule(string $rule): string
    {
        if (!in_array($rule, ['CASCADE', 'SET NULL', 'NO ACTION', 'RESTRICT'])) {
            throw new \InvalidArgumentException("Invalid rule: $rule in foreign key constraint.");
        }
        return $rule;
    }
}

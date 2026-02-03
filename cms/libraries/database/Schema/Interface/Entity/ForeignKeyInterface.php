<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface\Entity;

interface ForeignKeyInterface
{
    /**
     * Get
     * 
     * @return string
     */
    public function getName(): string;

    /**
     * Get
     * 
     * @return string
     */
    public function getTableName(): string;

    /**
     * Get
     * 
     * @return string
     */
    public function getColumnName(): string;

    /**
     * Get
     * 
     * @return string
     */
    public function getDeleteRule(): string;

    /**
     * Set
     * 
     * @param string $rule
     * 
     * @return self
     */
    public function setDeleteRule(string $rule): self;

    /**
     * Get
     * 
     * @return string
     */
    public function getUpdateRule(): string;

    /**
     * Set
     * 
     * @param string $rule
     * 
     * @return self
     */
    public function setUpdateRule(string $rule): self;

    /**
     * Get
     * 
     * @return string
     */
    public function getReferencedDatabaseName(): string;

    /**
     * Get
     * 
     * @return string
     */
    public function getReferencedTableName(): string;

    /**
     * Get
     * 
     * @return string
     */
    public function getReferencedColumnName(): string;

    /**
     * Set
     * 
     * @return self
     */
    public function setReferencedColumn(string $table_name, string $column_name): self;

    /**
     * Is
     * 
     * @param ForeignKeyInterface $foreign_key
     * 
     * @return bool
     */
    public function isEqual(ForeignKeyInterface $foreign_key): bool;

    /**
     * Get
     * 
     * @return string
     */
    public function getQueryStatement(): string;

    /**
     * Get
     * 
     * @return string
     */
    public function getAlterStatement(): string;

    /**
     * To array
     * 
     * @return string
     */
    public function toArray(): array;
}

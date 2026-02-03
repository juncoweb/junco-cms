<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface\Entity;

interface TableInterface
{
    /**
     * Get
     * 
     * @return string
     */
    public function getName(): string;

    /**
     * Set
     * 
     * @param ?string $name
     * 
     * @return self
     */
    public function setName(?string $name): self;

    /**
     * Get
     * 
     * @return ?string
     */
    public function getEngine(): ?string;

    /**
     * Set
     * 
     * @param ?string $engine
     * 
     * @return self
     */
    public function setEngine(?string $engine): self;

    /**
     * Get
     * 
     * @return ?string
     */
    public function getCollation(): ?string;

    /**
     * Set
     * 
     * @param ?string $collation
     * 
     * @return self
     */
    public function setCollation(?string $collation): self;

    /**
     * Get
     * 
     * @return int
     */
    public function getLength(): int;

    /**
     * Get
     * 
     * @return ?int
     */
    public function getAutoIncrement(): ?int;

    /**
     * Set
     * 
     * @param ?int $auto_increment
     * 
     * @return self
     */
    public function setAutoIncrement(?int $auto_increment): self;
    /**
     * Get
     * 
     * @return ?string
     */
    public function getComment(): ?string;

    /**
     * Set
     * 
     * @param ?string $comment
     * 
     * @return self
     */
    public function setComment(?string $comment): self;

    /**
     * Get
     * 
     * @return ColumnInterface[]
     */
    public function getColumns(): array;

    /**
     * Set
     * 
     * @param ColumnInterface[]
     * 
     * @return self
     */
    public function setColumns(array $columns = []): self;

    /**
     * Get
     * 
     * @return ColumnInterface
     */
    public function addColumn(string $name, ?string $type = null): ColumnInterface;
    /**
     * Get
     * 
     * @return IndexInterface[]
     */
    public function getIndexes(): array;
    /**
     * Set
     * 
     * @param IndexInterface[]
     * 
     * @return self
     */
    public function setIndexes(array $indexes = []): self;

    /**
     * Add
     * 
     * @param string $name
     * 
     * @return IndexInterface
     */
    public function addIndex(string $name): IndexInterface;
    /**
     * Get
     * 
     * @return ForeignKeyInterface[]
     */
    public function getForeignKeys(): array;

    /**
     * Set
     * 
     * @param ForeignKeyInterface[]
     * 
     * @return self
     */
    public function setForeignKeys(array $foreign_keys = []): self;

    /**
     * Get
     * 
     * @return string
     */
    public function getCreateStatement(): string;

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

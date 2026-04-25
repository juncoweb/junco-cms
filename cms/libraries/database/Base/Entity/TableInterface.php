<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Base\Entity;

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
     * @return static
     */
    public function setName(?string $name): static;

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
     * @return static
     */
    public function setEngine(?string $engine): static;

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
     * @return static
     */
    public function setCollation(?string $collation): static;

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
     * @return static
     */
    public function setAutoIncrement(?int $auto_increment): static;

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
     * @return static
     */
    public function setComment(?string $comment): static;

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
     * @return static
     */
    public function setColumns(array $columns = []): static;

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
     * @return static
     */
    public function setIndexes(array $indexes = []): static;

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
     * @return static
     */
    public function setForeignKeys(array $foreign_keys = []): static;

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

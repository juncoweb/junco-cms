<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface\Entity;

interface IndexInterface
{
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
    public function getName(): string;

    /**
     * Get
     * 
     * @return string
     */
    public function getType(): string;
    /**
     * Set
     * 
     * @return self
     */
    public function setType(?string $type): self;

    /**
     * Is Null
     * 
     * @return bool
     */
    public function isUnique(): bool;

    /**
     * Set
     * 
     * @return self
     */
    public function setUnique(bool $unique = true): self;

    /**
     * Is Null
     * 
     * @return bool
     */
    public function isNull(): bool;

    /**
     * Set
     * 
     * @return self
     */
    public function setNull(bool $null = true): self;

    /**
     * Get
     * 
     * @return string
     */
    public function getComment(): string;

    /**
     * Set
     * 
     * @return self
     */
    public function setComment(?string $comment): self;

    /**
     * Get
     * 
     * @return ?string
     */
    public function getCollation(): ?string;

    /**
     * Set
     * 
     * @return self
     */
    public function setCollation(?string $collation): self;

    /**
     * Get
     * 
     * @return int
     */
    public function getCardinality(): int;

    /**
     * Set
     * 
     * @return self
     */
    public function setCardinality(int $cardinality = 0): self;

    /**
     * Get
     * 
     * @return string
     */
    public function getIndexType(): string;

    /**
     * Set
     * 
     * @return self
     */
    public function setIndexType(int $index_type = 0): self;

    /**
     * Get
     * 
     * @return array
     */
    public function getColumns(): array;

    /**
     * Set
     * 
     * @return self
     */
    public function setColumns(array $columns = []): self;

    /**
     * Add
     * 
     * @return IndexColumnInterface
     */
    public function addColumn(string $name, int $sequence = 0, ?string $comment = null): IndexColumnInterface;

    /**
     * Get
     * 
     * @return string
     */
    public function getQueryStatement(): string;

    /**
     * Get
     * 
     * @param bool $indexExixts
     * 
     * @return string
     */
    public function getAlterStatement(bool $indexExixts = false): string;

    /**
     * To array
     * 
     * @return string
     */
    public function toArray(): array;
}

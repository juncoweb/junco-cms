<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Base\Entity;

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
     * @return static
     */
    public function setType(?string $type): static;

    /**
     * Is Null
     * 
     * @return bool
     */
    public function isUnique(): bool;

    /**
     * Set
     * 
     * @return static
     */
    public function setUnique(bool $unique = true): static;

    /**
     * Is Null
     * 
     * @return bool
     */
    public function isNull(): bool;

    /**
     * Set
     * 
     * @return static
     */
    public function setNull(bool $null = true): static;

    /**
     * Get
     * 
     * @return string
     */
    public function getComment(): string;

    /**
     * Set
     * 
     * @return static
     */
    public function setComment(?string $comment): static;

    /**
     * Get
     * 
     * @return ?string
     */
    public function getCollation(): ?string;

    /**
     * Set
     * 
     * @return static
     */
    public function setCollation(?string $collation): static;

    /**
     * Get
     * 
     * @return int
     */
    public function getCardinality(): int;

    /**
     * Set
     * 
     * @return static
     */
    public function setCardinality(int $cardinality = 0): static;

    /**
     * Get
     * 
     * @return string
     */
    public function getIndexType(): string;

    /**
     * Set
     * 
     * @return static
     */
    public function setIndexType(int $index_type = 0): static;

    /**
     * Get
     * 
     * @return array
     */
    public function getColumns(): array;

    /**
     * Set
     * 
     * @return static
     */
    public function setColumns(array $columns = []): static;

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

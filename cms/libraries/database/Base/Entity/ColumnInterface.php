<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Base\Entity;

interface ColumnInterface
{
    const NONE   = '';
    const ADD    = 'ADD';
    const MODIFY = 'MODIFY';
    const CHANGE = 'CHANGE';

    /**
     * Get
     * 
     * @return string
     */
    public function getName(): string;

    /**
     * Set
     * 
     * @return static
     */
    public function setName(string $name): static;

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
    public function setType(string $type): static;

    /**
     * Get
     * 
     * @return ?string
     */
    public function getTypeLength(): ?string;

    /**
     * Set
     * 
     * @return static
     */
    public function setTypeLength(?string $length = null): static;

    /**
     * Get
     * 
     * @return ?string
     */
    public function getAttribute(): ?string;

    /**
     * Set
     * 
     * @return static
     */
    public function setAttribute(?string $attribute = null): static;

    /**
     * Get
     * 
     * @return ?string
     */
    public function getFullType(): ?string;

    /**
     * Set
     * 
     * @return static
     */
    public function setFullType(string $type): static;

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
    public function setCollation(?string $collation = null): static;

    /**
     * Get
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
     * @return bool
     */
    public function isPri(): bool;

    /**
     * Get
     * 
     * @return string
     */
    public function getKey(): string;

    /**
     * Get
     * 
     * @return mixed
     */
    public function getDefault(): mixed;

    /**
     * Set
     * 
     * @return static
     */
    public function setDefault(?string $default = null): static;

    /**
     * Get
     * 
     * @return ?string
     */
    public function getExtra(): ?string;

    /**
     * Set
     * 
     * @return static
     */
    public function setExtra(?string $extra = null): static;

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
    public function setComment(?string $comment = null): static;

    /**
     * Set
     * 
     * @return static
     */
    public function setAction(string $action, string $column_name = ''): static;

    /**
     * Set
     * 
     * @return static
     */
    public function setPosition(?string $position = null): static;

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

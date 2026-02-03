<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface\Entity;

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
     * @return self
     */
    public function setName(string $name): self;

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
    public function setType(string $type): self;

    /**
     * Get
     * 
     * @return ?string
     */
    public function getTypeLength(): ?string;

    /**
     * Set
     * 
     * @return self
     */
    public function setTypeLength(?string $length = null): self;

    /**
     * Get
     * 
     * @return ?string
     */
    public function getAttribute(): ?string;

    /**
     * Set
     * 
     * @return self
     */
    public function setAttribute(?string $attribute = null): self;

    /**
     * Get
     * 
     * @return ?string
     */
    public function getFullType(): ?string;

    /**
     * Set
     * 
     * @return self
     */
    public function setFullType(string $type): self;

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
    public function setCollation(?string $collation = null): self;

    /**
     * Get
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
     * @return self
     */
    public function setDefault(?string $default = null): self;

    /**
     * Get
     * 
     * @return ?string
     */
    public function getExtra(): ?string;

    /**
     * Set
     * 
     * @return self
     */
    public function setExtra(?string $extra = null): self;

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
    public function setComment(?string $comment = null): self;

    /**
     * Set
     * 
     * @return self
     */
    public function setAction(string $action, string $column_name = ''): self;

    /**
     * Set
     * 
     * @return self
     */
    public function setPosition(?string $position = null): self;

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

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Base\Entity;

interface DatabaseInterface
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
     * @param string $name
     * 
     * @return static
     */
    public function setName(string $name): static;

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
    public function setCollation(?string $collation = null): static;

    /**
     * Get
     * 
     * @return string
     */
    public function getCreateStatement(): string;

    /**
     * To array
     * 
     * @return string
     */
    public function toArray(): array;
}

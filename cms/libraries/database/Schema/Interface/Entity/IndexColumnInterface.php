<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface\Entity;

interface IndexColumnInterface
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
     * @return ?string
     */
    public function getSubPart(): ?string;

    /**
     * Set
     * 
     * @return self
     */
    public function setSubPart(?int $sub_part = null): self;

    /**
     * Get
     * 
     * @return int
     */
    public function getSequence(): int;

    /**
     * Get
     * 
     * @return ?string
     */
    public function getComment(): ?string;

    /**
     * Get
     * 
     * @return string
     */
    public function getQueryStatement(): string;

    /**
     * To array
     * 
     * @return string
     */
    public function toArray(): array;
}

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface\Entity;

interface RoutineInterface
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
    public function getType(): string;

    /**
     * Get
     * 
     * @return string
     */
    public function getParameters(): string;

    /**
     * Set
     * 
     * @return self
     */
    public function setParameters(string $parameters): self;

    /**
     * Get
     * 
     * @return string
     */
    public function getReturns(): string;

    /**
     * Set
     * 
     * @return self
     */
    public function setReturns(string $returns): self;

    /**
     * Get
     * 
     * @return string
     */
    public function getDefinition(): string;

    /**
     * Set
     * 
     * @return self
     */
    public function setDefinition(?string $definition): self;

    /**
     * Get
     * 
     * @return ?string
     */
    public function getComment(): ?string;

    /**
     * Set
     * 
     * @return self
     */
    public function setComment(?string $comment): self;

    /**
     * Get
     * 
     * @return string
     */
    public function getCreateStatement(): string;

    /**
     * To array
     * 
     * @return array
     */
    public function toArray(): array;
}

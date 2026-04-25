<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Base\Entity;

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
     * @return static
     */
    public function setParameters(string $parameters): static;

    /**
     * Get
     * 
     * @return string
     */
    public function getReturns(): string;

    /**
     * Set
     * 
     * @return static
     */
    public function setReturns(string $returns): static;

    /**
     * Get
     * 
     * @return string
     */
    public function getDefinition(): string;

    /**
     * Set
     * 
     * @return static
     */
    public function setDefinition(?string $definition): static;

    /**
     * Get
     * 
     * @return ?string
     */
    public function getComment(): ?string;

    /**
     * Set
     * 
     * @return static
     */
    public function setComment(?string $comment): static;

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

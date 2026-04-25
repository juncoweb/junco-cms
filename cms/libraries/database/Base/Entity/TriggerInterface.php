<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Base\Entity;

interface TriggerInterface
{
    /**
     * Get
     * 
     * @return string
     */
    public function getName(): string;

    /**
     * set
     * 
     * @return static
     */
    public function setName(string $name): static;

    /**
     * Get
     * 
     * @return string
     */
    public function getEvent(): string;

    /**
     * set
     * 
     * @return static
     */
    public function setEvent(string $event): static;

    /**
     * Get
     * 
     * @return string
     */
    public function getTable(): string;

    /**
     * set
     * 
     * @return static
     */
    public function setTable(string $table): static;

    /**
     * Get
     * 
     * @return string
     */
    public function getTiming(): string;

    /**
     * set
     * 
     * @return static
     */
    public function setTiming(string $timing): static;

    /**
     * Get
     * 
     * @return string
     */
    public function getDefinition(): string;

    /**
     * set
     * 
     * @return static
     */
    public function setDefinition(string $definition): static;

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

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface\Entity;

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
     * @return self
     */
    public function setName(string $name): self;

    /**
     * Get
     * 
     * @return string
     */
    public function getEvent(): string;

    /**
     * set
     * 
     * @return self
     */
    public function setEvent(string $event): self;

    /**
     * Get
     * 
     * @return string
     */
    public function getTable(): string;

    /**
     * set
     * 
     * @return self
     */
    public function setTable(string $table): self;

    /**
     * Get
     * 
     * @return string
     */
    public function getTiming(): string;

    /**
     * set
     * 
     * @return self
     */
    public function setTiming(string $timing): self;

    /**
     * Get
     * 
     * @return string
     */
    public function getDefinition(): string;

    /**
     * set
     * 
     * @return self
     */
    public function setDefinition(string $definition): self;

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

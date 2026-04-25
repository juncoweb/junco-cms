<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Base\Entity;

abstract class DatabaseBase implements DatabaseInterface
{
    protected string  $name;
    protected ?string $collation = null;

    /**
     * Constructor
     */
    public function __construct(string $name, ?string $collation = null)
    {
        $this->name      = $name;
        $this->collation = $collation;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set
     * 
     * @param string $name
     * 
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get
     * 
     * @return ?string
     */
    public function getCollation(): ?string
    {
        return $this->collation;
    }

    /**
     * Set
     * 
     * @param ?string $collation
     * 
     * @return static
     */
    public function setCollation(?string $collation = null): static
    {
        $this->collation = $collation;
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getCreateStatement(): string
    {
        $charset = explode('_', $this->collation)[0];

        return "CREATE DATABASE `$this->name` DEFAULT CHARACTER SET $charset COLLATE $this->collation";
    }

    /**
     * To array
     * 
     * @return string
     */
    public function toArray(): array
    {
        return [
            'Name'       => $this->name,
            'Collation'  => $this->collation
        ];
    }
}

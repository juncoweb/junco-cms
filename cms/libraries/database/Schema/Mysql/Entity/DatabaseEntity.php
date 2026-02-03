<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql\Entity;

use Junco\Database\Schema\Interface\Entity\DatabaseEntityInterface;

class DatabaseEntity extends Base implements DatabaseEntityInterface
{
    /**
     * Constructor
     */
    public function __construct(
        protected string  $name,
        protected ?string $collation = null
    ) {}

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
     * @return self
     */
    public function setName(string $name): self
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
     * @return self
     */
    public function setCollation(?string $collation = null): self
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

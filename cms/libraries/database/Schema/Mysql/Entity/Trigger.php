<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql\Entity;

use Junco\Database\Schema\Interface\Entity\TriggerInterface;

class Trigger extends Base implements TriggerInterface
{
    /**
     * Constructor
     */
    public function __construct(
        protected string $name,
        protected string $table      = '',
        protected string $timing     = '',
        protected string $event      = '',
        protected string $definition = ''
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
     * set
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
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * set
     * 
     * @return self
     */
    public function setEvent(string $event): self
    {
        $this->event = $event;
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * set
     * 
     * @return self
     */
    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getTiming(): string
    {
        return $this->timing;
    }

    /**
     * set
     * 
     * @return self
     */
    public function setTiming(string $timing): self
    {
        $this->timing = $timing;
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getDefinition(): string
    {
        return $this->definition;
    }

    /**
     * set
     * 
     * @return self
     */
    public function setDefinition(string $definition): self
    {
        $this->definition = $definition;
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getCreateStatement(): string
    {
        return "CREATE TRIGGER `$this->name` $this->timing $this->event ON `$this->table` FOR EACH ROW $this->definition";
    }

    /**
     * To array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'Name'       => $this->name,
            'Table'      => $this->table,
            'Timing'     => $this->timing,
            'Event'      => $this->event,
            'Definition' => $this->definition
        ];
    }

    /**
     * From
     * 
     * @return ?self
     */
    public static function from(array $data): ?self
    {
        return new self(
            $data['Name'],
            $data['Table'],
            $data['Timing'],
            $data['Event'],
            $data['Definition']
        );
    }
}

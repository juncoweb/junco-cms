<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Base\Entity;

abstract class TriggerBase implements TriggerInterface
{
    protected string $name;
    protected string $table      = '';
    protected string $timing     = '';
    protected string $event      = '';
    protected string $definition = '';

    /**
     * Constructor
     */
    public function __construct(
        string $name,
        string $table      = '',
        string $timing     = '',
        string $event      = '',
        string $definition = ''
    ) {
        $this->name       = $name;
        $this->table      = $table;
        $this->timing     = $timing;
        $this->event      = $event;
        $this->definition = $definition;
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
     * set
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
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * set
     * 
     * @return static
     */
    public function setEvent(string $event): static
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
     * @return static
     */
    public function setTable(string $table): static
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
     * @return static
     */
    public function setTiming(string $timing): static
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
     * @return static
     */
    public function setDefinition(string $definition): static
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
     * @return ?static
     */
    public static function from(array $data): ?static
    {
        return new static(
            $data['Name'],
            $data['Table'],
            $data['Timing'],
            $data['Event'],
            $data['Definition']
        );
    }
}

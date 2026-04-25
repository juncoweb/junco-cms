<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Base\Entity;

abstract class RoutineBase implements RoutineInterface
{
    protected string  $type;
    protected string  $name;
    protected string  $definition = '';
    protected ?string $comment    = null;
    protected string  $parameters = '';
    protected string  $returns    = '';

    /**
     * Constructor
     */
    public function __construct(
        string  $type,
        string  $name,
        string  $definition = '',
        ?string $comment    = null,
        string  $parameters = '',
        string  $returns    = ''
    ) {
        $this->type       = $type;
        $this->name       = $name;
        $this->definition = $definition;
        $this->comment    = $comment;
        $this->parameters = $parameters;
        $this->returns    = $returns;
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
     * Get
     * 
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getParameters(): string
    {
        return $this->parameters;
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setParameters(string $parameters): static
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getReturns(): string
    {
        return $this->returns;
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setReturns(string $returns): static
    {
        $this->returns = $returns;
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
     * Set
     * 
     * @return static
     */
    public function setDefinition(?string $definition): static
    {
        $this->definition = $definition;
        return $this;
    }

    /**
     * Get
     * 
     * @return ?string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setComment(?string $comment): static
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getCreateStatement(): string
    {
        return $this->type === 'PROCEDURE'
            ? "CREATE PROCEDURE `$this->name`($this->parameters)\n$this->definition"
            : "CREATE FUNCTION `$this->name`($this->parameters) RETURNS $this->returns\n$this->definition";
    }

    /**
     * To array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'Type'       => $this->type,
            'Name'       => $this->name,
            'Definition' => $this->definition,
            'Comment'    => $this->comment,
            'Parameters' => $this->parameters,
            'Returns'    => $this->returns
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
            $data['Type'],
            $data['Name'],
            $data['Definition'],
            $data['Comment'],
            $data['Parameters'],
            $data['Returns']
        );
    }
}

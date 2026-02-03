<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql\Entity;

use Junco\Database\Schema\Interface\Entity\RoutineInterface;

class Routine extends Base implements RoutineInterface
{
    /**
     * Constructor
     */
    public function __construct(
        protected string  $type,
        protected string  $name,
        protected string  $definition = '',
        protected ?string $comment    = null,
        protected string  $parameters = '',
        protected string  $returns    = ''
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
     * @return self
     */
    public function setParameters(string $parameters): self
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
     * @return self
     */
    public function setReturns(string $returns): self
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
     * @return self
     */
    public function setDefinition(?string $definition): self
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
     * @return self
     */
    public function setComment(?string $comment): self
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
     * @return ?self
     */
    public static function from(array $data): ?self
    {
        return new self(
            $data['Type'],
            $data['Name'],
            $data['Definition'],
            $data['Comment'],
            $data['Parameters'],
            $data['Returns']
        );
    }
}

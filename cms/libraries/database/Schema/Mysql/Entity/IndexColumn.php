<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql\Entity;

use Junco\Database\Schema\Interface\Entity\IndexColumnInterface;

class IndexColumn extends Base implements IndexColumnInterface
{
    /**
     * Constructor
     */
    public function __construct(
        protected string  $name,
        protected int     $sequence = 0,
        protected ?string $comment  = null,
        protected ?int    $sub_part = null
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
     * @return ?string
     */
    public function getSubPart(): ?string
    {
        return $this->sub_part;
    }

    /**
     * Set
     * 
     * @return self
     */
    public function setSubPart(?int $sub_part = null): self
    {
        $this->sub_part = $sub_part;
        return $this;
    }

    /**
     * Get
     * 
     * @return int
     */
    public function getSequence(): int
    {
        return $this->sequence;
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
     * Get
     * 
     * @return string
     */
    public function getQueryStatement(): string
    {
        $statement = '`' . $this->name . '`';

        if ($this->sub_part) {
            $statement .= '(' . $this->sub_part . ')';
        }

        return $statement;
    }

    /**
     * To array
     * 
     * @return string
     */
    public function toArray(): array
    {
        return [
            'Name'     => $this->name,
            'Sequence' => $this->sequence,
            'Comment'  => $this->comment,
            'SubPart'  => $this->sub_part,
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
            $data['Sequence'],
            $data['Comment'],
            $data['SubPart']
        );
    }
}

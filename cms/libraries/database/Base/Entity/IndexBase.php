<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Base\Entity;

use Error;

abstract class IndexBase implements IndexInterface
{
    // vars
    protected string  $tbl_name;
    protected string  $name;
    protected ?bool   $is_unique   = null;
    protected bool    $is_null     = false;
    protected ?string $comment     = '';
    protected ?string $collation   = null;
    protected int     $cardinality = 0;
    protected ?string $index_type  = null;
    //
    protected ?string $type        = null;
    protected array   $columns     = [];

    /**
     * Constructor
     */
    public function __construct(
        string  $tbl_name,
        string  $name,
        ?bool   $is_unique   = null,
        bool    $is_null     = false,
        ?string $comment     = '',
        ?string $collation   = null,
        int     $cardinality = 0,
        ?string $index_type  = null
    ) {
        $this->tbl_name    = $tbl_name;
        $this->name        = $name;
        $this->is_unique   = $is_unique;
        $this->is_null     = $is_null;
        $this->comment     = $comment;
        $this->collation   = $collation;
        $this->cardinality = $cardinality;
        $this->index_type  = $index_type;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tbl_name;
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
        return $this->type ??= $this->getCalculatedType();
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Is Null
     * 
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->is_unique ??= $this->getCalculatedUnique();
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setUnique(bool $unique = true): static
    {
        $this->is_unique = $unique;
        return $this;
    }

    /**
     * Is Null
     * 
     * @return bool
     */
    public function isNull(): bool
    {
        return $this->is_null;
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setNull(bool $null = true): static
    {
        $this->is_null = $null;
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getComment(): string
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
     * @return ?string
     */
    public function getCollation(): ?string
    {
        return $this->collation;
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setCollation(?string $collation): static
    {
        $this->collation = $collation;
        return $this;
    }

    /**
     * Get
     * 
     * @return int
     */
    public function getCardinality(): int
    {
        return $this->cardinality;
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setCardinality(int $cardinality = 0): static
    {
        $this->cardinality = $cardinality;
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getIndexType(): string
    {
        return $this->index_type;
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setIndexType(int $index_type = 0): static
    {
        $this->index_type = $index_type;
        return $this;
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setColumns(array $columns = []): static
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Add
     * 
     * @return IndexColumnInterface
     */
    //public function addColumn(string $name, int $sequence = 0, ?string $comment = null): IndexColumnInterface;

    /**
     * Get
     * 
     * @return string
     */
    public function getQueryStatement(): string
    {
        $Columns = $this->getColumnsStatement($this->columns);

        if ($this->name == 'PRIMARY') {
            return "PRIMARY KEY ($Columns)";
        }

        $type = $this->getType();

        if ($type == 'INDEX') {
            $type = 'KEY'; // ?
        }

        return "$type `$this->name` ($Columns)";
    }

    /**
     * Get
     * 
     * @param bool $indexExixts
     * 
     * @return string
     */
    public function getAlterStatement(bool $indexExixts = false): string
    {
        $Columns = $this->getColumnsStatement($this->columns);

        if ($this->name == 'PRIMARY') {
            return $indexExixts
                ? "ALTER TABLE `$this->tbl_name` DROP PRIMARY KEY, ADD PRIMARY KEY ($Columns)"
                : "ALTER TABLE `$this->tbl_name` ADD PRIMARY KEY ($Columns)";
        }

        $type = $this->getType();

        return $indexExixts
            ? "ALTER TABLE `$this->tbl_name` DROP INDEX `$this->name`, ADD $type `$this->name` ($Columns)"
            : "ALTER TABLE `$this->tbl_name` ADD $type `$this->name` ($Columns)";
    }

    /**
     * To array
     * 
     * @return string
     */
    public function toArray(): array
    {
        $columns = array_map(
            fn(IndexColumnInterface $column) => $column->toArray(),
            $this->columns
        );

        return [
            'Name'        => $this->name,
            'Type'        => $this->type,
            'IsUnique'    => $this->is_unique,
            'IsNull'      => $this->is_null,
            'Comment'     => $this->comment,
            'Collation'   => $this->collation,
            'Cardinality' => $this->cardinality,
            'IndexType'   => $this->index_type,
            'Columns'     => $columns
        ];
    }

    /**
     * From
     * 
     * @param string $table_name
     * @param array  $data
     * 
     * @return ?static
     */
    public static function from(string $table_name, array $data): ?static
    {
        return (new static(
            $table_name,
            $data['Name'],
            $data['IsUnique'],
            $data['IsNull'],
            $data['Comment'],
            $data['Collation'],
            $data['Cardinality'],
            $data['IndexType']
        ))->setType($data['Type']);
    }

    /**
     * Get
     * 
     * @return string
     */
    protected function getCalculatedType(): string
    {
        if ($this->name == 'PRIMARY') {
            return 'PRIMARY';
        }

        if ($this->index_type == 'FULLTEXT') {
            return 'FULLTEXT';
        }

        if ($this->is_unique === null) {
            throw new Error("Index type is not defined for index {$this->name}");
        }

        return $this->is_unique
            ? 'UNIQUE'
            : 'INDEX';
    }

    /**
     * Get
     * 
     * @return bool
     */
    protected function getCalculatedUnique(): bool
    {
        if ($this->name == 'PRIMARY') {
            return true;
        }

        if ($this->type === null) {
            throw new Error("Index uniqueness is not defined for index {$this->name}");
        }

        return $this->type == 'UNIQUE';
    }

    /**
     * Get
     * 
     * @param array $columns
     * 
     * @return string
     */
    public function getColumnsStatement(array $columns): string
    {
        return implode(', ', array_map(
            fn(IndexColumnInterface $column) => $column->getQueryStatement(),
            $columns
        ));
    }
}

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql\Entity;

use Junco\Database\Schema\Interface\Entity\IndexInterface;
use Error;

class Index extends Base implements IndexInterface
{
    // vars
    protected ?string $type    = null;
    protected ?string $query   = null;
    protected array   $columns = [];

    /**
     * Constructor
     */
    public function __construct(
        protected string  $tbl_name,
        protected string  $name,
        protected ?bool   $is_unique   = null,
        protected bool    $is_null     = false,
        protected ?string $comment     = '',
        protected ?string $collation   = null,
        protected int     $cardinality = 0,
        protected ?string $index_type  = null
    ) {}

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
     * @return self
     */
    public function setType(?string $type): self
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
     * @return self
     */
    public function setUnique(bool $unique = true): self
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
     * @return self
     */
    public function setNull(bool $null = true): self
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
     * @return ?string
     */
    public function getCollation(): ?string
    {
        return $this->collation;
    }

    /**
     * Set
     * 
     * @return self
     */
    public function setCollation(?string $collation): self
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
     * @return self
     */
    public function setCardinality(int $cardinality = 0): self
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
     * @return self
     */
    public function setIndexType(int $index_type = 0): self
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
     * @return self
     */
    public function setColumns(array $columns = []): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Add
     * 
     * @return IndexColumnInterface
     */
    public function addColumn(string $name, int $sequence = 0, ?string $comment = null): IndexColumn
    {
        if (!$sequence) {
            $sequence = count($this->columns) + 1;
        }

        return $this->columns[] = new IndexColumn($name, $sequence, $comment);
    }

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
            fn(IndexColumn $column) => $column->toArray(),
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
     * @return ?self
     */
    public static function from(string $table_name, array $data): ?self
    {
        $index = new self(
            $table_name,
            $data['Name'],
            $data['IsUnique'],
            $data['IsNull'],
            $data['Comment'],
            $data['Collation'],
            $data['Cardinality'],
            $data['IndexType']
        );
        $index->setType($data['Type']);
        $index->setColumns(array_map(
            fn(array $column_data) => IndexColumn::from($column_data),
            $data['Columns']
        ));

        return $index;
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
            fn(IndexColumn $column) => $column->getQueryStatement(),
            $columns
        ));
    }
}

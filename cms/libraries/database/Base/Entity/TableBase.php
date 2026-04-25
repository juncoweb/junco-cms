<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Base\Entity;

abstract class TableBase implements TableInterface
{
    // vars
    protected string  $name;
    protected ?string $comment = null;
    protected ?string $engine = null;
    protected ?string $collation = null;
    protected int     $length = 0;
    protected ?int    $auto_increment = null;
    //
    protected array $columns      = [];
    protected array $indexes      = [];
    protected array $foreign_keys = [];

    /**
     * Constructor
     */
    public function __construct(
        string  $name,
        ?string $comment = null,
        ?string $engine = null,
        ?string $collation = null,
        int     $length = 0,
        ?int    $auto_increment = null,
    ) {
        $this->name           = $name;
        $this->comment        = $comment;
        $this->engine         = $engine;
        $this->collation      = $collation;
        $this->length         = $length;
        $this->auto_increment = $auto_increment;
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
     * @param ?string $name
     * 
     * @return static
     */
    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get
     * 
     * @return ?string
     */
    public function getEngine(): ?string
    {
        return $this->engine;
    }

    /**
     * Set
     * 
     * @param ?string $engine
     * 
     * @return static
     */
    public function setEngine(?string $engine): static
    {
        $this->engine = $engine;
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
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * Get
     * 
     * @return ?int
     */
    public function getAutoIncrement(): ?int
    {
        return $this->auto_increment;
    }

    /**
     * Set
     * 
     * @param ?int $auto_increment
     * 
     * @return static
     */
    public function setAutoIncrement(?int $auto_increment): static
    {
        $this->auto_increment = $auto_increment;
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
     * @param ?string $comment
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
     * @return ColumnInterface[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Set
     * 
     * @param ColumnInterface[]
     * 
     * @return static
     */
    public function setColumns(array $columns = []): static
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Get
     * 
     * @return ColumnInterface
     */
    //public function addColumn(string $name, ?string $type = null): ColumnInterface;

    /**
     * Get
     * 
     * @return IndexInterface[]
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * Set
     * 
     * @param IndexInterface[]
     * 
     * @return static
     */
    public function setIndexes(array $indexes = []): static
    {
        $this->indexes = $indexes;
        return $this;
    }

    /**
     * Add
     * 
     * @param string $name
     * 
     * @return IndexInterface
     */
    //public function addIndex(string $name): IndexInterface;

    /**
     * Get
     * 
     * @return ForeignKeyInterface[]
     */
    public function getForeignKeys(): array
    {
        return $this->foreign_keys;
    }

    /**
     * Set
     * 
     * @param ForeignKeyInterface[]
     * 
     * @return static
     */
    public function setForeignKeys(array $foreign_keys = []): static
    {
        $this->foreign_keys = $foreign_keys;
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getCreateStatement(): string
    {
        $definition = [];

        if ($this->columns) {
            $definition[] = $this->getColumnsStatement($this->columns);
        }

        if ($this->indexes) {
            $definition[] = $this->getIndexesStatement($this->indexes);
        }

        if ($this->foreign_keys) {
            $definition[] = $this->getForeignKeysStatement($this->foreign_keys);
        }

        //
        $definition = implode(",\n  ", $definition);
        $options    = implode("  ", $this->getOptionsStatement());

        return "CREATE TABLE IF NOT EXISTS `$this->name` (\n  $definition\n) $options";
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getAlterStatement(): string
    {
        $definition = $this->getOptionsStatement();

        if ($this->columns) {
            $definition[] = $this->getColumnsStatement($this->columns);
        }

        if ($this->indexes) {
            $definition[] = $this->getIndexesStatement($this->indexes);
        }

        if ($this->foreign_keys) {
            $definition[] = $this->getForeignKeysStatement($this->foreign_keys);
        }

        $definition = implode(", ", $definition);

        return "ALTER TABLE `$this->name` {$definition}";
    }

    /**
     * To array
     * 
     * @return array
     */
    public function toArray(): array
    {
        $columns = array_map(
            fn(ColumnInterface $column) => $column->toArray(),
            $this->columns
        );

        $indexes = array_map(
            fn(IndexInterface $index) => $index->toArray(),
            $this->indexes
        );

        $foreign_keys = array_map(
            fn(ForeignKeyInterface $foreign_key) => $foreign_key->toArray(),
            $this->foreign_keys
        );

        return [
            'Name'        => $this->name,
            'Comment'     => $this->comment,
            'Engine'      => $this->engine,
            'Collation'   => $this->collation,
            'Columns'     => $columns,
            'Indexes'     => $indexes,
            'ForeignKeys' => $foreign_keys
        ];
    }

    /**
     * From
     * 
     * @return ?static
     */
    public static function from(array $data): ?static
    {
        return new static($data['Name'], $data['Comment'], $data['Engine'], $data['Collation']);
    }

    /**
     * Get
     */
    protected function getColumnsStatement(array $columns): string
    {
        return implode(",\n  ", array_map(
            fn(ColumnInterface $column) => $column->getQueryStatement(),
            $columns
        ));
    }

    /**
     * Get
     */
    protected function getIndexesStatement(array $indexes): string
    {
        return implode(",\n  ", array_map(
            fn(IndexInterface $index) => $index->getQueryStatement(),
            $indexes
        ));
    }

    /**
     * Get
     */
    protected function getForeignKeysStatement(array $foreign_keys): string
    {
        return implode(",\n  ", array_map(
            fn(ForeignKeyInterface $foreign_key) => $foreign_key->getQueryStatement(),
            $foreign_keys
        ));
    }

    /**
     * Get
     */
    public function getOptionsStatement(): array
    {
        $options = [];

        if ($this->engine) {
            $options[] = "ENGINE=$this->engine";
        }

        if ($this->collation) {
            $options[] = $this->getCollationStatement($this->collation);
        }

        if ($this->comment !== null) {
            $options[] = "COMMENT='$this->comment'";
        }

        if ($this->auto_increment) {
            $options[] = "AUTO_INCREMENT=$this->auto_increment";
        }

        return $options;
    }

    /**
     * Get
     */
    protected function getCollationStatement(string $collation): string
    {
        $charset = explode('_', $collation)[0];

        return "DEFAULT CHARACTER SET $charset COLLATE $collation";
    }
}

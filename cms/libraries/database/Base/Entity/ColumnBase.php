<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Base\Entity;

abstract class ColumnBase implements ColumnInterface
{
    //
    protected string  $tbl_name;
    protected string  $name;
    protected ?string $type      = null;
    protected ?string $length    = null;
    protected ?string $collation = null;
    protected bool    $is_null   = false;
    protected string  $key       = '';
    protected ?string $attribute = null;
    protected mixed   $default   = null;
    protected ?string $extra     = null;
    protected ?string $comment   = null;
    //
    protected string  $action    = '';
    protected ?string $position  = null;

    /**
     * Constructor
     */
    public function __construct(
        string  $tbl_name,
        string  $name,
        ?string $type      = null,
        ?string $collation = null,
        bool    $is_null   = false,
        string  $key       = '',
        mixed   $default   = null,
        ?string $extra     = null,
        ?string $comment   = null
    ) {
        $this->tbl_name  = $tbl_name;
        $this->name      = $name;
        $this->collation = $collation;
        $this->is_null   = $is_null;
        $this->key       = $key;
        $this->default   = $default;
        $this->extra     = $extra;
        $this->comment   = $comment;

        if ($type) {
            $this->setFullType($type);
        }
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get
     * 
     * @return ?string
     */
    public function getTypeLength(): ?string
    {
        return $this->length;
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setTypeLength(?string $length = null): static
    {
        $this->length = $length;
        return $this;
    }

    /**
     * Get
     * 
     * @return ?string
     */
    public function getAttribute(): ?string
    {
        return $this->attribute;
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setAttribute(?string $attribute = null): static
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * Get
     * 
     * @return ?string
     */
    public function getFullType(): ?string
    {
        $type = $this->type;

        if ($this->length) {
            $type .= '(' . $this->length . ')';
        }

        if ($this->attribute) {
            $type .= ' ' . $this->attribute;
        }

        return $type;
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setFullType(string $type): static
    {
        preg_match('/^([a-z]+)(?:\((?<TypeLength>.*?)\))?\s?(?<Attribute>.*)?$/i', $type, $match);

        $this->type      = $match[1];
        $this->length    = $match['TypeLength'] ?? null;
        $this->attribute = $match['Attribute'] ?? null;
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
    public function setCollation(?string $collation = null): static
    {
        $this->collation = $collation;
        return $this;
    }

    /**
     * Get
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
     * @return bool
     */
    public function isPri(): bool
    {
        return $this->key == 'PRI';
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get
     * 
     * @return mixed
     */
    public function getDefault(): mixed
    {
        return $this->default;
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setDefault(?string $default = null): static
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Get
     * 
     * @return ?string
     */
    public function getExtra(): ?string
    {
        return $this->extra;
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setExtra(?string $extra = null): static
    {
        // AUTO_INCREMENT
        // DEFAULT_GENERATED
        // on update CURRENT_TIMESTAMP
        // VIRTUAL GENERATED or STORED GENERATED
        $this->extra = $extra;

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
    public function setComment(?string $comment = null): static
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setAction(string $action, string $column_name = ''): static
    {
        switch ($action) {
            case 'ADD':
            case 'MODIFY':
                $this->action = $action;
                break;
            case 'CHANGE':
                $this->action = 'CHANGE `' . ($column_name ?: $this->name) . '`';
                break;
        }

        return $this;
    }

    /**
     * Set
     * 
     * @return static
     */
    public function setPosition(?string $position = null): static
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getQueryStatement(): string
    {
        $sql = '';

        if ($this->action) {
            $sql .= $this->action . ' ';
        }

        $sql .= "`$this->name` " . $this->getFullType();

        if ($this->collation) {
            $sql .= $this->getCollationStatement($this->collation);
        }

        $sql .= $this->is_null ? ' NULL' : ' NOT NULL';

        if ($this->default !== null) {
            $sql .= $this->getDefaultStatement($this->default);
        }

        if ($this->extra) {
            $sql .= $this->getExtraStatement($this->extra);
        }

        if ($this->comment) {
            $sql .= " COMMENT '$this->comment'";
        }

        if ($this->position) {
            $sql .= $this->position == 'FIRST'
                ? " $this->position"
                : " AFTER `$this->position`";
        }

        return $sql;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getAlterStatement(): string
    {
        return "ALTER TABLE `$this->tbl_name` " . $this->getQueryStatement();
    }

    /**
     * To array
     * 
     * @return string
     */
    public function toArray(): array
    {
        return [
            'Name'      => $this->name,
            'Type'      => $this->type,
            'Length'    => $this->length,
            'Attribute' => $this->attribute,
            'Collation' => $this->collation,
            'IsNull'    => $this->is_null,
            'Default'   => $this->default,
            'Extra'     => $this->extra,
            'Comment'   => $this->comment
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
            $data['Type'],
            $data['Collation'],
            $data['IsNull'],
            '',
            $data['Default'],
            $data['Extra'],
            $data['Comment']
        ))
            ->setTypeLength($data['Length'])
            ->setAttribute($data['Attribute']);
    }

    /**
     * Get
     */
    protected function getCollationStatement(string $collation): string
    {
        $Charset = explode('_', $collation)[0];

        return " CHARACTER SET $Charset COLLATE $collation";
    }

    /**
     * Get
     */
    protected function getDefaultStatement(string $default): string
    {
        if (!in_array($default, ['NULL', 'CURRENT_TIMESTAMP']) && !is_numeric($default)) {
            $default = "'$default'";
        }

        return " DEFAULT $default";
    }

    /**
     * Get
     */
    protected function getExtraStatement(string $extra): string
    {
        $extra = str_replace('DEFAULT_GENERATED', '', $extra);

        return $extra
            ? " $extra"
            : "";
    }
}

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql\Entity;

use Junco\Database\Schema\Interface\Entity\ColumnInterface;

class Column extends Base implements ColumnInterface
{
    //
    protected ?string $length    = null;
    protected ?string $attribute = null;
    //
    protected string  $action    = '';
    protected ?string $position  = null;

    /**
     * Constructor
     */
    public function __construct(
        protected string  $tbl_name,
        protected string  $name,
        protected ?string $type      = null,
        protected ?string $collation = null,
        protected bool    $is_null   = false,
        protected string  $key       = '',
        protected mixed   $default   = null,
        protected ?string $extra     = null,
        protected ?string $comment   = null
    ) {
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set
     * 
     * @return self
     */
    public function setType(string $type): self
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
     * @return self
     */
    public function setTypeLength(?string $length = null): self
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
     * @return self
     */
    public function setAttribute(?string $attribute = null): self
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
     * @return self
     */
    public function setFullType(string $type): self
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
     * @return self
     */
    public function setDefault(?string $default = null): self
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
     * @return self
     */
    public function setExtra(?string $extra = null): self
    {
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
     * @return self
     */
    public function setComment(?string $comment = null): self
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Set
     * 
     * @return self
     */
    public function setAction(string $action, string $column_name = ''): self
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
     * @return self
     */
    public function setPosition(?string $position = null): self
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
     * @return ?self
     */
    public static function from(string $table_name, array $data): ?self
    {
        return (new self(
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

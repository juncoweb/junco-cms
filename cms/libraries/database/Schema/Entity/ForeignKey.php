<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Entity;

class ForeignKey
{
	/**
	 * Constructor
	 */
	public function __construct(
		protected string $TableName,
		protected string $FieldName,
		protected string $Name,
		protected string $ReferencedTable,
		protected string $ReferencedField
	) {}

	/**
	 * Get
	 */
	public function getTableName(): string
	{
		return $this->TableName;
	}

	/**
	 * Get
	 */
	public function getFieldName(): string
	{
		return $this->FieldName;
	}

	/**
	 * Get
	 */
	public function getName(): string
	{
		return $this->Name;
	}

	/**
	 * Get
	 */
	public function getReferencedTable(): string
	{
		return $this->ReferencedTable;
	}

	/**
	 * Get
	 */
	public function getReferencedField(): string
	{
		return $this->ReferencedField;
	}

	public function toArray(): array
	{
		return [
			'TableName' => $this->TableName,
			'FieldName' => $this->FieldName,
			'Name' => $this->Name,
			'ReferencedTable' => $this->ReferencedTable,
			'ReferencedField' => $this->ReferencedField,
		];
	}
}

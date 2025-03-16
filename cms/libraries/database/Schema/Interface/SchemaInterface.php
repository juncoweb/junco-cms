<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface;

interface SchemaInterface
{
	/**
	 * Get
	 * 
	 * @return array
	 */
	public function getInfo(): array;

	/**
	 * Database
	 * 
	 * @return _DatabaseInterface
	 */
	public function database(): _DatabaseInterface;

	/**
	 * Fields
	 * 
	 * @return FieldsInterface
	 */
	public function fields(): FieldsInterface;

	/**
	 * ForeignKeys
	 * 
	 * @return ForeignKeysInterface
	 */
	public function foreignKeys(): ForeignKeysInterface;

	/**
	 * Indexes
	 * 
	 * @return IndexesInterface
	 */
	public function indexes(): IndexesInterface;

	/**
	 * Registers
	 * 
	 * @return RegistersInterface
	 */
	public function registers(): RegistersInterface;

	/**
	 * Routines
	 * 
	 * @return RoutinesInterface
	 */
	public function routines(): RoutinesInterface;

	/**
	 * Tables
	 * 
	 * @return TablesInterface
	 */
	public function tables(): TablesInterface;

	/**
	 * Triggers
	 * 
	 * @return TriggersInterface
	 */
	public function triggers(): TriggersInterface;
}

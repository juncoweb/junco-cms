<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Exporter;

/**
 * Database Exporter Adapter Interface
 */
interface ExporterInterface
{
	/**
	 * Add a comment header with some reference data to the buffer
	 *
	 * @param array  $data
	 */
	public function addHeader(array $data = []): void;

	/**
	 * Add
	 *
	 * @param array $database
	 * @param bool  $add_drop_database
	 */
	public function addDatabase(array $database, bool $add_drop_database = false): void;

	/**
	 * Add a routines to the buffer
	 * 
	 * @param array $routines
	 * @param bool  $add_drop_routine
	 */
	public function addRoutines(array $routines, bool $add_drop_routine = false): void;

	/**
	 * Add the tables to the buffer
	 * 
	 * @param array $table
	 * @param bool  $add_drop_table
	 */
	public function addTable(array $table, bool $add_drop_table = false): void;

	/**
	 * Add registers into the buffer
	 * 
	 * @param array $data
	 * @param bool  $use_ignore
	 * @param int   $max_query_size
	 */
	public function addRegisters(array $data, bool $use_ignore = false, int $max_query_size = 0): void;

	/**
	 * Add triggers into the buffer
	 * 
	 * @param array $triggers
	 * @param bool  $add_drop_trigger
	 */
	public function addTriggers($triggers, bool $add_drop_trigger = false): void;

	/**
	 * Used to record history of changes.
	 * 
	 * @param string|array $history
	 */
	public function addHistory(string|array $history): void;

	/**
	 * Render
	 * 
	 * @return string|array
	 */
	public function render(): string|array;

	/**
	 * Write
	 * 
	 * @param string $file
	 */
	public function write(string $file): void;
}

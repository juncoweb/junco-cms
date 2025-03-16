<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface;

use Database;

interface IndexesInterface
{
	/**
	 * Constructor
	 */
	public function __construct(Database $db);

	/**
	 * Show Indexes
	 * 
	 * @param string $TableName
	 * @param array  $where
	 * 
	 * @return array
	 */
	public function fetchAll(string $TableName, array $where = []): array;

	/**
	 * Add Index
	 * 
	 * @param string $TableName
	 * @param string $IndexName
	 * @param array  $Index
	 * 
	 * @return int
	 */
	public function create(string $TableName, string $IndexName, array $Index): int;

	/**
	 * Drop Index
	 * 
	 * @param string $TableName
	 * @param string $IndexName
	 * @param array  $Index
	 * 
	 * @return int
	 */
	public function drop(string $TableName, string $IndexName, array $Index = []): int;
}

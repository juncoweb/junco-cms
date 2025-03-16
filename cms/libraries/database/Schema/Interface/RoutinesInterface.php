<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface;

use Database;

interface RoutinesInterface
{
	/**
	 * Constructor
	 */
	public function __construct(Database $db);

	/**
	 * Fetch All
	 * 
	 * @param array $param
	 * 
	 * @return array
	 */
	public function fetchAll(array $where = []): array;

	/**
	 * Show
	 * 
	 * @param string $Type   The values can be FUNCTION or PROCEDURE.
	 * @param string $Name
	 * 
	 * @return array
	 */
	public function showData(string $Type = '', string $Name = '', array $db_prefix_tables = []): array;

	/**
	 * Create
	 * 
	 * @param string $Type
	 * @param string $RoutineName
	 * @param array  $Routine
	 * 
	 * @return void
	 */
	public function create(string $Type, string $RoutineName, array $Routine): void;

	/**
	 * Drop
	 * 
	 * @param string $Type
	 * @param string $RoutineName
	 * 
	 * @return int
	 */
	public function drop(string $Type, string $RoutineName): int;
}

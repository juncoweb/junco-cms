<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface;

use Database;

interface RegistersInterface
{
	/**
	 * Constructor
	 */
	public function __construct(Database $db);

	/**
	 * show
	 * 
	 * @param string $tbl_name
	 * @param bool   $set_db_prefix
	 * 
	 * @return array
	 */
	public function showData(string $tbl_name, bool $set_db_prefix = false): array;
}

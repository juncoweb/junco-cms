<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter;

/**
 * Database PDO Trait
 */
trait PgsqlTrait
{
	/**
	 * Sanitize
	 * 
	 * @param string $query
	 * 
	 * @return void
	 */
	protected function sanitizeQuery(string &$query): void
	{
		$query = str_replace('`', '', $query);
		$query = preg_replace_callback('/(?<Limit>LIMIT)\s+(?<Value>\d+)\s*,\s*(?<Offset>\d+)/', function ($match) {
			if (isset($match['Limit'])) {
				return "LIMIT $match[Offset] OFFSET $match[Value]";
			}
		}, $query);
		//die($query);
	}

	/**
	 * Sanitize
	 * 
	 * @param string $query
	 * 
	 * @return void
	 */
	protected function sanitizeQueryToNativePg(string &$query): void
	{
		$this->sanitizeQuery($query);

		$count = 0;
		$query = preg_replace_callback('/(?<Holder>\?)/', function ($match) use (&$count) {
			if (isset($match['Holder'])) {
				return '$' . (++$count);
			}
		}, $query);
		//die($query);
	}
}

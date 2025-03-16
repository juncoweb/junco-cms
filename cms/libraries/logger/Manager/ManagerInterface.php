<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Logger\Manager;

interface ManagerInterface
{
	/**
	 * Gets all logs.
	 *
	 * @return array
	 */
	public function fetchAll(array $where = []): array;

	/**
	 * Fetch
	 *
	 * @param string $id
	 * 
	 * @return array|null
	 */
	public function fetch(string $id): array|null;

	/**
	 * Thin
	 * 
	 * @param bool $delete
	 * 
	 * @return bool
	 */
	public function thin(bool $delete = true): bool;

	/**
	 * Toggle the status of the registers.
	 * 
	 * @param string[] $id
	 * @param ?bool    $status
	 */
	public function status(array $id, ?bool $status = null): void;

	/**
	 * Delete
	 * 
	 * @param string[] $id
	 *
	 * @return bool
	 */
	public function deleteMultiple(array $id): bool;

	/**
	 * Clear
	 *
	 * @return bool
	 */
	public function clear(): bool;

	/**
	 * Verify
	 * 
	 * @param array $rows
	 * @param bool  $delete
	 * 
	 * @return array
	 */
	public function verifyDuplicates(array $rows, bool $delete = true): array;
}

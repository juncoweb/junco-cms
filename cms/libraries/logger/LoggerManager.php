<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Logger;

use Junco\Logger\Manager\ManagerInterface;

class LoggerManager
{
	protected ManagerInterface $adapter;

	/**
	 * Constructor
	 */
	public function __construct(?ManagerInterface $adapter = null)
	{
		$this->adapter = $adapter ?? $this->getAdapter();
	}

	/**
	 * Get
	 */
	protected function getAdapter()
	{
		return new Manager\FileManager;
		/* switch (null) {
			default:
			case 'file':
				return new Manager\FileManager;
		} */
	}

	/**
	 * Gets all logs.
	 *
	 * @return array
	 */
	public function fetchAll(array $where = []): array
	{
		return $this->adapter->fetchAll($where);
	}

	/**
	 * Fetch
	 *
	 * @param string $id
	 * 
	 * @return array|null
	 */
	public function fetch(string $id): array|null
	{
		return $this->adapter->fetch($id);
	}

	/**
	 * Thin
	 * 
	 * @param bool $delete
	 * 
	 * @return bool
	 */
	public function thin(bool $delete = true): bool
	{
		return $this->adapter->thin($delete);
	}

	/**
	 * Toggle the status of the registers.
	 * 
	 * @param string[] $id
	 * @param ?bool    $status
	 */
	public function status(array $id, ?bool $status = null): void
	{
		$this->adapter->status($id, $status);
	}

	/**
	 * Delete multiple
	 * 
	 * @param string[] $id
	 *
	 * @return bool
	 */
	public function deleteMultiple(array $id): bool
	{
		return $this->adapter->deleteMultiple($id);
	}

	/**
	 * Clear
	 *
	 * @return bool
	 */
	public function clear(): bool
	{
		return $this->adapter->clear();
	}

	/**
	 * Get
	 * 
	 * @param array $id
	 * 
	 * @return array
	 */
	public function getReports(array $id = []): array
	{
		$reports = [];
		$rows = $this->adapter->verifyDuplicates(
			$this->adapter->fetchAll($id ? ['id' => $id] : [])
		);

		foreach ($rows as $row) {
			$this->extractFromContext($row, ['file', 'line', 'backtrace']);

			$reports[] = [
				'level'			=> $row['level'],
				'message'		=> $this->shortenFile($row['message']),
				'file'			=> $row['file'],
				'line'			=> $row['line'],
				'backtrace' 	=> $row['backtrace'],
				'created_at'	=> $row['created_at'],
			];
		}

		return $reports;
	}

	/**
	 * Extracts data from the context.
	 * 
	 * @param string $row
	 * @param array  $extract    The keys to be extracted.
	 */
	public function extractFromContext(array &$row, array $extract): void
	{
		if (empty($row['context']) || empty($extract)) {
			return;
		}

		$context = json_decode($row['context'], true);

		foreach ($extract as $key) {
			if (isset($context[$key])) {
				$row[$key] = $this->shortenFile($context[$key]);
				unset($context[$key]);
			} else {
				$row[$key] = '';
			}
		}

		$row['context'] = $context;
	}

	/**
	 * Get
	 */
	protected function shortenFile(string $file): string
	{
		return str_replace(SYSTEM_ABSPATH, '', $file);
	}
}

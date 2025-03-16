<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Usys;

use SystemHelper;
use Plugins;

class MFACollector
{
	//
	protected array $rows = ['' => ''];

	/**
	 * Get
	 */
	public function add(string $key, string $caption): void
	{
		$this->rows[$key] = $caption;
	}

	/**
	 * Get
	 */
	public function fetchAll(): array
	{
		return $this->rows;
	}

	/**
	 * Get
	 */
	public static function getAll(): array
	{
		$collector = new self();
		$plugins = SystemHelper::scanPlugins('mfa');

		if ($plugins) {
			Plugins::get('mfa', 'options', $plugins)?->run($collector);
		}

		return $collector->fetchAll();
	}
}

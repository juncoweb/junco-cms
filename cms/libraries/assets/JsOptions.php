<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Assets;

class JsOptions
{
	// vars
	protected string $file;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->file = SYSTEM_STORAGE . 'assets/options.js';
	}

	/**
	 * Get
	 * 
	 * @return string
	 */
	public function get(): string
	{
		return is_readable($this->file)
			? file_get_contents($this->file)
			: '';
	}

	/**
	 * Get
	 * 
	 * @return bool
	 */
	public function put(string $content): bool
	{
		return false !== file_put_contents($this->file, $content);
	}
}

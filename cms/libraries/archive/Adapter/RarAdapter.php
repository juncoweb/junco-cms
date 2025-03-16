<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Archive\Adapter;

use \RarArchive;
use \Exception;

class RarAdapter implements AdapterInterface
{
	// vars
	protected $rar = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		if (!extension_loaded('rar')) {
			throw new Exception(_t('The Archive class has no support to execute the task.'));
		}

		$this->rar = new RarArchive();
	}

	/**
	 * Extract
	 *
	 * @param string $file		The full path to the file.
	 * @param string $dir		The directory where the package will be extracted.
	 * @param bool   $delete	Option to delete the compressed file.
	 *
	 * @throws Exception
	 */
	public function extract(string $file, string $dir): void
	{
		if (!$this->rar->open($file)) {
			throw new Exception(_t('The archive could not be opened.'));
		}
		if (!$this->rar->extract($dir)) {
			throw new Exception(_t('The archive could not be extracted.'));
		}
		if (!$this->rar->close()) {
			throw new Exception(_t('The archive could not be closed.'));
		}
	}

	/**
	 * Compress
	 * 
	 * @param string $file		The full path to the file to be created.
	 * @param string $dir		The base directory.
	 * @param array  $nodes		Select only some directories or files from the base directory. 
	 */
	public function compress(string $file, string $dir, array $nodes): void
	{
		throw new Exception(_t('The Archive class has no support to execute the task.'));
	}
}

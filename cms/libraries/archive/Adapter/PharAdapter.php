<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Archive\Adapter;

use \Phar;
use \PharData;
use \Exception;

class PharAdapter implements AdapterInterface
{
	// vars
	protected $phar			= null;
	protected $file			= null;
	protected $tmp_file		= null;
	protected $compression	= null;
	protected $accept = [
		'phar' => 'phar',
		'tar' => 'tar',
	];

	/**
	 * Constructor
	 */
	public function __construct()
	{
		if (!extension_loaded('phar')) {
			throw new Exception(_t('The Archive class has no support to execute the task.'));
		}
		if (extension_loaded('zlib')) {
			// gz
			$this->accept['gz'] = 'gz';
			$this->accept['gzip'] = 'gz';
			// tar.gz
			$this->accept['tgz'] = 'gz';
			$this->accept['tgzip'] = 'gz';
		}
		if (extension_loaded('bz2')) {
			// bz
			$this->accept['bz'] = 'bz';
			$this->accept['bzip'] = 'bz';
			$this->accept['bzip2'] = 'bz';
			$this->accept['bz2'] = 'bz';
			// tar.bz
			$this->accept['tbz'] = 'bz';
			$this->accept['tbzip'] = 'bz';
			$this->accept['tbz2'] = 'bz';
			$this->accept['tbzip2'] = 'bz';
		}
	}

	/**
	 * Extract
	 *
	 * @param string $file		The full path to the file.
	 * @param string $dir		The directory where the package will be extracted.
	 * @param bool   $delete	Option to delete the compressed file.
	 */
	public function extract(string $file, string $dir): void
	{
		if (!isset($this->accept[pathinfo($file, PATHINFO_EXTENSION)])) {
			throw new Exception(_t('The Archive class has no support to execute the task.'));
		}

		(new PharData($file))->extractTo($dir, null, true);
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
		$this->create($file);
		$this->addNodes($dir, $nodes);
		$this->close();
	}

	/**
	 * Create
	 * 
	 * @param string $file
	 */
	protected function create(string $file): void
	{
		$info				= pathinfo($file);
		$this->tmp_file		= $info['dirname'] . '/~tmp_' . uniqid();
		$this->file			= $info['dirname'] . '/' . $info['basename'];
		$extension			= $info['extension'];

		if (!isset($this->accept[$extension])) {
			throw new Exception(_t('The Archive class has no support to execute the task.'));
		}

		switch ($this->accept[$extension]) {
			case 'gz':
				$this->compression = [Phar::GZ, '.gz'];
				break;

			case 'bz':
				$this->compression = [Phar::BZ2, '.bz2'];
				break;
		}

		if ($extension == 'phar') {
			$format = Phar::PHAR;
			$this->tmp_file	.= '.phar';
		} else {
			$format = Phar::TAR;
			$this->tmp_file	.= '.tar';
		}

		$this->phar = new PharData($this->tmp_file, 0, null, $format);
	}

	/**
	 * Add Nodes
	 * 
	 * @param string $dir
	 * @param array  $nodes
	 */
	protected function addNodes(string $dir, array $nodes): void
	{
		if ($nodes) {
			foreach ($nodes as $node) {
				// $node  = trim($node, '\\/');
				$cnode = $dir . $node;

				if (is_dir($cnode)) {
					$this->addDir($cnode . '/', $node . '/');
				} elseif (is_file($cnode)) {
					$this->phar->addFile($cnode, $node);
				}
			}
		} elseif (is_dir($dir)) {
			$this->addDir($dir . '/');
		}
	}

	/**
	 * Add directory
	 * 
	 * @param string $dir
	 * @param string $localname
	 */
	protected function addDir(string $dir, string $localname = ''): void
	{
		if ($localname) {
			$this->phar->addEmptyDir($localname);
		}

		foreach ($this->scandir($dir) as $node) {
			$cnode = $dir . $node;

			if (is_dir($cnode)) {
				$this->addDir($cnode . '/', $localname . $node . '/');
			} else if (is_file($cnode)) {
				$this->phar->addFile($cnode, $localname . $node);
			}
		}
	}

	/**
	 * Scandir
	 * 
	 * @param string $dir
	 * 
	 * @return array
	 */
	protected function scandir(string $dir): array
	{
		$cdir = is_readable($dir) ? scandir($dir) : false;
		if ($cdir) {
			return array_diff($cdir, ['.', '..']);
		}
		return [];
	}

	/**
	 * Close
	 */
	protected function close(): void
	{
		if ($this->compression) {
			$this->phar->compress($this->compression[0]);
			unlink($this->tmp_file);
			$this->tmp_file .= $this->compression[1];
		}
		//is_file($this->file) and unlink($this->file);
		rename($this->tmp_file, $this->file);

		$this->phar = null;
	}
}

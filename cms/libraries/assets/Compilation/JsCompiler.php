<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Assets\Compilation;

use Filesystem;
use Plugin;

class JsCompiler
{
	// vars
	protected string     $abspath;
	protected Filesystem $fs;
	protected ?Plugin    $minifier = null;

	/**
	 * Constructor
	 * 
	 * @param ?string $abspath
	 */
	public function __construct(?string $abspath = null)
	{
		$this->abspath = $abspath ?? SYSTEM_ABSPATH;
		$this->fs      = new Filesystem($this->abspath);
	}

	/**
	 * Compile
	 *
	 * @param string $target  The file where the compiled file will be saved.
	 * @param array  $files   An array with all the files to compile.
	 * @param bool   $minify  Enable the minimizer.
	 *
	 * @return bool
	 */
	public function compile(string $target, array $files, bool $minify = false): bool
	{
		$buffer	= '';

		foreach ($files as $file) {
			if ($this->isJsFile($file)) {
				$buffer .= $this->fs->getContent($file);
			}
		}

		$result = $this->fs->putContent($target, $buffer);

		if ($result && $minify) {
			$this->minify($target);
		}

		return $result;
	}

	/**
	 * Get
	 *
	 * @param string $file
	 *
	 * @return bool
	 */
	protected function isJsFile(string $file): bool
	{
		return is_file($this->abspath . $file)
			&& pathinfo($file, PATHINFO_EXTENSION) == 'js';
	}

	/**
	 * Minify
	 *
	 * @param string $file
	 *
	 * @return void
	 */
	protected function minify(string $file): void
	{
		$this->minifier ??= Plugin::get('minifier', 'minify', config('assets.jsmin_plugin'));
		$this->minifier?->run($this->abspath . $file);
	}
}

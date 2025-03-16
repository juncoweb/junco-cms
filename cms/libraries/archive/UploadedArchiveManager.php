<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Archive;

use Junco\Filesystem\UploadedFileManager;
use Archive;

class UploadedArchiveManager extends UploadedFileManager
{
	/**
	 * Validate
	 *
	 * @param ?array $rules
	 */
	public function validate(?array $rules = null): self
	{
		parent::validate(
			array_merge([
				'allow_extensions' => (new Archive)->acceptsToExtract()
			], $rules ?: [])
		);

		return $this;
	}

	/**
	 * Extract
	 * 
	 * @param bool $delete
	 */
	public function extract(bool $delete = false): void
	{
		$archive = new Archive($this->dirpath);

		foreach ($this->files as $file) {
			$archive->extract($file['filename'], '', $delete);
		}
	}
}

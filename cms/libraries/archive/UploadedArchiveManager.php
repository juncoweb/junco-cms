<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
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
     * 
     * @return static
     */
    public function validate(?array $rules = null): static
    {
        return parent::validate(
            array_merge([
                'allow_extensions' => (new Archive)->acceptsToExtract()
            ], $rules ?: [])
        );
    }

    /**
     * Extract
     * 
     * @param bool $delete
     * 
     * @return void
     */
    public function extract(bool $delete = false): void
    {
        $archive = new Archive($this->dirpath);

        foreach ($this->files as $file) {
            $archive->extract($file['filename'], '', $delete);
        }
    }
}

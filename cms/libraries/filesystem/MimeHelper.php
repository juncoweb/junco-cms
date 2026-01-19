<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem;

use finfo;

class MimeHelper
{
    // vars
    protected ?string $mimetypes    = null;
    protected ?finfo  $finfo        = null;

    /**
     * Get mime type from file name.
     * 
     * @param string $file
     * 
     * @return string|false
     */
    public function getType(string $file): string|false
    {
        return $this->getTypeFromFile($file)
            ?: $this->getTypeFromExtension($file);
    }

    /**
     * Get mime type from file info.
     * 
     * @param string $file
     * 
     * @return string|false
     */
    public function getTypeFromFile(string $file): string|false
    {
        if (!is_file($file)) {
            return false;
        }

        $this->finfo ??= new finfo(FILEINFO_MIME_TYPE);

        return $this->finfo->file($file);
    }

    /**
     * Get mime type from extension.
     * 
     * @param string $file
     * 
     * @return string|false
     */
    public function getTypeFromExtension(string $file): string|false
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION) ?: basename($file);

        if ($extension) {
            $pattern = '%(.*?)(?:\t)+(?:\w+\s)*(?:' . strtolower($extension) . ')(?:\s|$)%';

            if (preg_match($pattern, $this->getMimetypes(), $matches)) {
                return $matches[1];
            }
        }

        return false;
    }

    /**
     * Get extension from mime type.
     * 
     * @param string $mimetype
     * 
     * @return array|false
     */
    public function getExtension(string $mimetype): array|false
    {
        $pattern = '%(?:' . preg_quote($mimetype, '%') . ')(?:\t)*(.*)%';

        if (preg_match($pattern, $this->getMimetypes(), $matches)) {
            if ($matches[1]) {
                return explode(' ', $matches[1]);
            }
        }

        return false;
    }

    /**
     * Get the mime type sheet.
     * 
     * @return string
     */
    protected function getMimetypes(): string
    {
        return $this->mimetypes ??= file_get_contents(__DIR__ . '/MimeTypes/mime.types') ?: '';
    }
}

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Archive\Adapter;

interface AdapterInterface
{
    /**
     * Extract
     *
     * @param string $file		The full path to the file.
     * @param string $dir		The directory where the package will be extracted.
     * @param bool   $delete	Option to delete the compressed file.
     */
    public function extract(string $file, string $dir): void;

    /**
     * Compress
     * 
     * @param string $file		The full path to the file to be created.
     * @param string $dir		The base directory.
     * @param array  $nodes		Select only some directories or files from the base directory. 
     */
    public function compress(string $file, string $dir, array $nodes): void;
}

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\System;

class Etc
{
    // vars
    private string $dirpath = SYSTEM_STORAGE . 'etc/';

    /**
     * Store
     */
    public function store(string $file, mixed $data)
    {
        is_dir($this->dirpath) or mkdir($this->dirpath, SYSTEM_MKDIR_MODE, true);

        if (false === file_put_contents($this->dirpath . $file, $data)) {
            throw new \Exception(_t('Failed to write the target file.'));
        }
    }
}

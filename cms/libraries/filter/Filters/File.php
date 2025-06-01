<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

use Junco\Filesystem\UploadedFileManager;
use Psr\Http\Message\UploadedFileInterface;

class File extends FileFilterAbstract
{
    /**
     * Filter
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function filter(mixed $value, UploadedFileInterface|array|null $file = null, mixed $altValue = null): mixed
    {
        $manager = new UploadedFileManager($file);

        if ($value) {
            $manager->keepCurrent();
        } elseif ($this->required) {
            $this->required = false;

            $manager->verifyIsEmpty();
        }

        foreach ($this->callback as $fn) {
            $fn($manager);
        }

        return $manager;
    }
}

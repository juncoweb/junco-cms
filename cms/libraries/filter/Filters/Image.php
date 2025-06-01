<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

use Junco\Filesystem\UploadedImageManager;
use Psr\Http\Message\UploadedFileInterface;

class Image extends FileFilterAbstract
{
    /**
     * Filter
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function filter(mixed $value, ?UploadedFileInterface $file = null, mixed $altValue = null): mixed
    {
        $manager = new UploadedImageManager($file);

        if ($value) {
            $manager->keepCurrent();
        } else {
            if ($this->required) {
                $this->required = false;

                $manager->verifyIsEmpty();
            }
            $manager->validate();
        }

        foreach ($this->callback as $fn) {
            $fn($manager);
        }

        return $manager;
    }
}

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

use Junco\Filesystem\UploadedImageManager;
use Psr\Http\Message\UploadedFileInterface;

class Images extends FileFilterAbstract
{
    // vars
    public    bool  $altValue = true;
    protected array $__data = [];

    /**
     * Filter
     * 
     * @param mixed $value
     * @param mixed $file
     * @param mixed $altValue
     * 
     * @return mixed
     */
    public function filter(mixed $value, ?UploadedFileInterface $file = null, mixed $altValue = null): mixed
    {
        $manager = new UploadedImageManager($file, true);

        if ($altValue) {
            $manager->setOrder($altValue);
        }

        if ($this->required) {
            $this->required = false;

            $manager->verifyIsEmpty();
        }
        $manager->validate();

        foreach ($this->callback as $fn) {
            $fn($manager);
        }

        return $manager;
    }
}

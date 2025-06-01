<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

use Junco\Archive\UploadedArchiveManager;
use Psr\Http\Message\UploadedFileInterface;

class Archive extends FileFilterAbstract
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
        $manager = new UploadedArchiveManager($file);

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

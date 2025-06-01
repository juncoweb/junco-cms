<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

use Psr\Http\Message\UploadedFileInterface;

interface FilterInterface
{
    /**
     * Set modifiers
     * 
     * @param array $modifiers
     * 
     * @return void
     */
    public function setModifiers(array $modifiers): void;

    /**
     * Filter
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function filter(mixed $value, ?UploadedFileInterface $file = null, mixed $altValue = null): mixed;
}

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Exception;

use Exception;
use Throwable;

interface HttpThrowableInterface
{
    /**
     * Get
     */
    public function getStatusCode(): int;
}

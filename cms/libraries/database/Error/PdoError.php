<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Error;

use Error;
use PDOException;

class PdoError extends Error
{
    /**
     * Constructor
     */
    public function __construct(PDOException $e)
    {
        parent::__construct($e->getMessage(), 0, $e);
    }
}

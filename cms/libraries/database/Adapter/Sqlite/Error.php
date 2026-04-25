<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter\Sqlite;

use Error as BaseError;
use Exception;
use SQLite3Exception;

class Error extends BaseError
{
    /**
     * Constructor
     */
    public function __construct(Exception|SQLite3Exception $e)
    {
        parent::__construct($e->getMessage(), 0, $e);
    }
}

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Error;

use Error;
use mysqli_sql_exception;

class MysqlError extends Error
{
    /**
     * Constructor
     */
    public function __construct(mysqli_sql_exception $e)
    {
        parent::__construct($e->getMessage(), 0, $e);
    }
}

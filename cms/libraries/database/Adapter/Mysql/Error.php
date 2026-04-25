<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter\Mysql;

use Error as BaseError;
use mysqli_sql_exception;

class Error extends BaseError
{
    /**
     * Constructor
     */
    public function __construct(mysqli_sql_exception $e)
    {
        parent::__construct($e->getMessage(), 0, $e);
    }
}

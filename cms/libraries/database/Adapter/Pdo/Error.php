<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter\Pdo;

use Error as BaseError;
use PDOException;

class Error extends BaseError
{
    /**
     * Constructor
     */
    public function __construct(PDOException $e)
    {
        parent::__construct($e->getMessage(), 0, $e);
    }
}

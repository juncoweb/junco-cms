<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class None extends FilterAbstract
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->default = null;
    }
}

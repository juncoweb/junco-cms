<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Integer extends FilterAbstract
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->type = 'int';
        $this->default  = 0;
        $this->argument = [
            'filter' => FILTER_VALIDATE_INT,
            'options' => ['default' => 0]
        ];
    }
}

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Email extends FilterAbstract
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->type = 'string';
        $this->default  = '';
        $this->argument = [
            'filter' => FILTER_VALIDATE_EMAIL,
        ];
    }
}

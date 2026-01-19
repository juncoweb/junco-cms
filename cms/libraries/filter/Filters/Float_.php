<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Float_ extends FilterAbstract
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->type = 'float';
        $this->default  = 0;
        $this->argument = [
            'filter' => FILTER_VALIDATE_FLOAT,
            'options' => ['default' => 0]
        ];
    }
}

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Callback extends FilterAbstract
{
    /**
     * Constructor
     * 
     * @param string|array|callable $filter_value
     */
    public function __construct(string|array|callable $filter_value)
    {
        $this->type = 'mixed';
        $this->accept = ['default', 'array', 'or_use', 'only_if', 'only_if_not', 'required'];
        $this->argument = [
            'filter' => FILTER_CALLBACK,
            'options' => $filter_value
        ];
    }
}

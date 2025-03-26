<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
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
        $this->argument = [
            'filter' => FILTER_CALLBACK,
            'options' => $filter_value
        ];
    }

    /**
     * Set modifiers
     * 
     * @param array $modifiers
     */
    public function setModifiers(array $modifiers): void
    {
        $this->accept($modifiers, ['default', 'array', 'required', 'only_if', 'only_if_not']);

        parent::setModifiers($modifiers);
    }
}

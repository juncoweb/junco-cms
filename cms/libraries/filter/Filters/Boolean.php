<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Boolean extends FilterAbstract
{
    /**
     * Constructor
     * 
     * @param string|array|null $filter_value
     */
    public function __construct(string|array|null $filter_value = null)
    {
        $this->type = 'bool';
        $this->default  = false;
        $this->accept = ['array', 'only_if', 'only_if_not', 'required'];
        $this->argument = [
            'filter' => FILTER_VALIDATE_BOOLEAN
        ];

        if ($filter_value) {
            if (is_string($filter_value)) {
                $filter_value = $this->strToArr($filter_value, '/');
            }

            if (count($filter_value) != 2) {
                throw new \Exception('There is an error in the bool filter values.');
            }

            if (
                is_numeric($filter_value[0])
                && is_numeric($filter_value[1])
            ) {
                $filter_value[0] = (int)$filter_value[0];
                $filter_value[1] = (int)$filter_value[1];
            }

            $this->default = $filter_value[0];
            $this->callback[] = function (&$value) use ($filter_value) {
                $value = $filter_value[$value ? 1 : 0];
            };
        }
    }
}

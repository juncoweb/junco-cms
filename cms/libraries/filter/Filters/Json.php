<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Json extends FilterAbstract
{
    /**
     * Constructor
     * 
     * @param string|array|null $filter_value
     */
    public function __construct(string|array|null $filter_value = null)
    {
        $this->type = 'string';
        $this->default = null;
        $this->accept = ['min', 'max', 'array', 'default', 'or_use', 'only_if', 'only_if_not', 'required'];
        $this->argument = [
            'filter' => FILTER_CALLBACK,
            'options' => function ($value) {
                if ($value === null) {
                    return null;
                }
                if (json_validate($value)) {
                    return $value;
                }

                return false;
            }
        ];

        if ($filter_value) {
            $arg = $this->strToArr($filter_value);
            $filter_value = $arg[0];
            $depth = $arg[1] ?? 512;

            $this->afterCallback[] = function (&$value) use ($filter_value, $depth) {
                switch ($filter_value) {
                    case 'decode':
                        $value = json_decode($value, false, $depth);
                        break;

                    case  'decode_a':
                        $value = $value ? json_decode($value, true, $depth) : [];
                        break;
                }
            };
        }
    }
}

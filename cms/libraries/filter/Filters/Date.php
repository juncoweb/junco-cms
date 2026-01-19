<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Date extends FilterAbstract
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->type = 'date';
        $this->default  = null;
        $this->accept = ['min', 'max', 'array', 'default', 'or_use', 'only_if', 'only_if_not', 'required'];
        $this->argument = [
            'filter' => FILTER_CALLBACK,
            'options' => [$this, 'validate']
        ];
    }

    /**
     * Validate
     */
    public function validate($value)
    {
        if ($value === null) {
            return null;
        }

        if (preg_match('#^\d{4}-\d{2}-\d{2}$#', $value)) {
            $part = explode('-', $value);
            if (checkdate($part[1], $part[2], $part[0])) {
                return $value;
            }
        }
        return false;
    }

    /**
     * Set
     */
    protected function setDefaultModifier(mixed $rule_value)
    {
        if ($rule_value) {
            throw new \Exception(sprintf(
                'The «%s» filter does not accept values in the «default» rule',
                __CLASS__
            ));
        }

        $this->default = date('Y-m-d');
    }
}

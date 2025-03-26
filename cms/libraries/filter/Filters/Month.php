<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Month extends FilterAbstract
{
    // const
    const MONTH_PATTERN = '/^\d{4}-(?:0[1-9]|1[1-2])$/';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->type = 'month';
        $this->default  = null;
        $this->argument = [
            'filter' => FILTER_VALIDATE_REGEXP,
            'options' => [
                'regexp' => self::MONTH_PATTERN
            ]
        ];
    }

    /**
     * Set modifiers
     * 
     * @param array $modifiers
     */
    public function setModifiers(array $modifiers): void
    {
        $this->accept($modifiers, ['array', 'default', 'required', 'only_if', 'only_if_not']);

        parent::setModifiers($modifiers);
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

        $this->default = date('Y-m');
    }
}

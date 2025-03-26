<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Time extends FilterAbstract
{
    // const
    const TIME_PATTERN = '/^(?:[0-1][0-9]|2[0-3]):[0-5][0-9]$/';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->type = 'time';
        $this->default  = null;
        $this->argument = [
            'filter' => FILTER_VALIDATE_REGEXP,
            'options' => [
                'regexp' => self::TIME_PATTERN
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

        $this->default = date('H:i');
    }
}

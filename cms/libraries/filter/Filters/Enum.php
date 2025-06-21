<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

use Error;

class Enum extends FilterAbstract
{
    protected string $enumName;

    /**
     * Constructor
     * 
     * @param string|array|callable $filter_value
     */
    public function __construct(string|array|callable $filter_value)
    {
        if (strpos($filter_value, '.') !== false) {
            $filter_value = $this->getEnumName($filter_value);
        }

        $this->enumName = $filter_value;
        $cases = $this->enumName::cases();

        $this->type = 'mixed';
        $this->default = null;
        $this->accept = ['default', 'array', 'or_use', 'only_if', 'only_if_not', 'required'];
        $this->argument = [
            'filter' => FILTER_CALLBACK,
            'options' => function ($value) use ($cases) {
                foreach ($cases as $case) {
                    if ($case->name === $value) {
                        return $case;
                    }
                }

                return false;
            }
        ];
    }
    /**
     * Set
     * 
     * @param mixed $rule_value
     */
    protected function setDefaultModifier(mixed $rule_value)
    {
        if ($rule_value) {
            $this->default = $this->enumName::{$rule_value};
        } else {
            if (!method_exists($this->enumName, 'default')) {
                throw new Error(sprintf('The enum «%s» does not have a default value.', $this->enumName));
            }

            $this->default = $this->enumName::default();
        }
    }
    /**
     * 
     */
    protected function getEnumName(string $value): string
    {
        $parts     = explode('.', $value, 2);
        $extension = ucfirst($parts[0]);
        $enumName  = $this->toCamelCase($parts[1] ?: $parts[0]);

        return "Junco\\{$extension}\\Enum\\{$enumName}";
    }

    /**
     * 
     */
    protected function toCamelCase(string $value): string
    {
        return implode(array_map('ucfirst', explode('_', $value)));
    }
}

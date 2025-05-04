<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Enum extends FilterAbstract
{
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

        $cases = array_column($filter_value::cases(), 'name');

        $this->type = 'mixed';
        $this->argument = [
            'filter' => FILTER_CALLBACK,
            'options' => function ($value) use ($cases) {
                if (!in_array($value, $cases)) {
                    return false;
                }

                return $value;
            }
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

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

use Psr\Http\Message\UploadedFileInterface;

abstract class FilterAbstract implements FilterInterface
{
    // vars
    public bool   $isArray     = false;
    public bool   $isFile      = false;
    public bool   $altValue    = false;
    public string $orUse       = '';
    public string $onlyIf      = '';
    public bool   $onlyIfValue = false;
    public mixed  $required    = false;
    //
    public array  $argument = ['filter' => FILTER_DEFAULT];

    //
    protected string $type          = 'int';
    protected bool   $isFirst       = false;
    protected mixed  $default       = false;
    protected array  $callback      = [];
    protected array  $afterCallback = [];
    protected ?array $accept        = null;

    /**
     * Set modifiers
     * 
     * @param array $modifiers
     * 
     * @throws \Exception
     */
    public function setModifiers(array $modifiers): void
    {
        if ($this->accept !== null) {
            $diff = array_diff(array_keys($modifiers), $this->accept);

            if ($diff) {
                throw new \Exception(sprintf(
                    'The filter «%s» does not accept the «%s» rule.',
                    get_class($this),
                    implode(', ', $diff)
                ));
            }
        }

        foreach ($modifiers as $rule_name => $rule_value) {
            switch ($rule_name) {
                case 'array':
                    $this->setArrayModifier($rule_value);
                    break;

                case 'default':
                    $this->setDefaultModifier($rule_value);
                    break;

                case 'max':
                    $this->setMaxModifier($rule_value);
                    break;

                case 'min':
                    $this->setMinModifier($rule_value);
                    break;

                case 'in':
                    $this->setInModifier($rule_value);
                    break;

                case 'or_use':
                    $this->setOrUseModifier($rule_value);
                    break;

                case 'only_if':
                    $this->setOnlyIfModifier($rule_value);
                    break;

                case 'only_if_not':
                    $this->setOnlyIfModifier($rule_value, false);
                    break;

                case 'required':
                    $this->setRequiredModifier($rule_value);
                    break;

                default:
                    throw new \Exception(sprintf('The filter «%s» does not accept the «%s» rule.', get_class($this), $rule_name));
            }
        }

        $this->callback += $this->afterCallback;
    }

    /**
     * Filter
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function filter(mixed $value, UploadedFileInterface|array|null $file = null, mixed $altValue = null): mixed
    {
        if ($this->isFirst) {
            $value = $value[0] ?? null;
        }

        if (null === $value || $this->isArray != is_array($value)) {
            return $this->isArray ? [] : $this->default;
        }

        // I apply the callbacks
        if ($this->isArray) {
            foreach ($value as $i => $item) {
                $value[$i] = $this->getFilteredValue($item);
            }
        } else {
            $value = $this->getFilteredValue($value);
        }

        return $value;
    }

    /**
     * callback
     */
    protected function getFilteredValue($value)
    {
        if ($value === false) {
            return $this->default;
        }

        foreach ($this->callback as $fn) {
            $fn($value);

            if ($value === false) {
                return $this->default;
            }
        }

        return $value;
    }

    /**
     * Set
     * 
     * @param ?string $rule_value
     */
    protected function setArrayModifier(?string $rule_value = null)
    {
        if (empty($this->argument['flags'])) {
            $this->argument['flags'] = FILTER_REQUIRE_ARRAY;
        } else {
            $this->argument['flags'] |= FILTER_REQUIRE_ARRAY;
        }

        if ($rule_value === 'first') {
            $this->isFirst = true;
        } else {
            $this->isArray = true;
        }
    }

    /**
     * Set
     * 
     * @param mixed $rule_value
     */
    protected function setDefaultModifier(mixed $rule_value)
    {
        switch ($this->type) {
            case 'int':
                $rule_value = (int)$rule_value;
                $this->argument['options']['default'] = $rule_value;
                break;

            case 'float':
                $rule_value = (float)$rule_value;
                $this->argument['options']['default'] = $rule_value;
                break;

            case 'string':
                $rule_value = (string)$rule_value;
                break;

            case 'mixed':
                if (is_numeric($rule_value)) {
                    $rule_value = (int)$rule_value;
                }
                break;

            default:
                throw new \Exception(sprintf(
                    'The filter «%s» does not accept the «%s» rule.',
                    get_class($this),
                    'min'
                ));
        }

        $this->default = $rule_value;
    }

    /**
     * Set
     * 
     * @param mixed $rule_value
     */
    protected function setMinModifier(mixed $rule_value)
    {
        switch ($this->type) {
            case 'int':
                $this->argument['options']['min_range'] = (int)$rule_value;
                break;

            case 'float':
                $this->argument['options']['min_range'] = (float)$rule_value;
                break;

            case 'string':
                $this->callback[] = function (string &$value) use ($rule_value) {
                    if (strlen($value) < (int)$rule_value) {
                        $value = false;
                    }
                };
                break;

            case 'date':
            case 'datetime':
                $rule_value = $this->getTimestap($rule_value);

                $this->callback[] = function (&$value) use ($rule_value) {
                    if (strtotime($value) < $rule_value) {
                        $value = false;
                    }
                };
                break;

            default:
                throw new \Exception(sprintf(
                    'The filter «%s» does not accept the «%s» rule.',
                    get_class($this),
                    'min'
                ));
        }
    }

    /**
     * Set
     * 
     * @param mixed $rule_value
     */
    protected function setMaxModifier(mixed $rule_value)
    {
        switch ($this->type) {
            case 'int':
                $this->argument['options']['max_range'] = (int)$rule_value;
                break;

            case 'float':
                $this->argument['options']['max_range'] = (float)$rule_value;
                break;

            case 'string':
                $this->callback[] = function (string &$value) use ($rule_value) {
                    if (strlen($value) > (int)$rule_value) {
                        $value = false;
                    }
                };
                break;

            case 'date':
            case 'datetime':
                $rule_value = $this->getTimestap($rule_value);

                $this->callback[] = function (&$value) use ($rule_value) {
                    if (strtotime($value) > $rule_value) {
                        $value = false;
                    }
                };
                break;

            default:
                throw new \Exception(sprintf(
                    'The filter «%s» does not accept the «%s» rule.',
                    get_class($this),
                    'max'
                ));
        }
    }

    /**
     * Set
     * 
     * @param string|array $rule_value
     */
    protected function setInModifier(string|array $rule_value)
    {
        if (is_string($rule_value)) {
            $rule_value = $this->strToArr($rule_value);
        }

        $this->callback[] = function (&$value) use ($rule_value) {
            if (!in_array($value, $rule_value)) {
                $value = false;
            }
        };
    }

    /**
     * Set
     * 
     * @param string $rule_value
     * @param bool   $value
     */
    protected function setOrUseModifier(string $rule_value, bool $value = true)
    {
        $this->orUse = $rule_value;
    }

    /**
     * Set
     * 
     * @param string $rule_value
     * @param bool   $value
     */
    protected function setOnlyIfModifier(string $rule_value, bool $value = true)
    {
        $this->onlyIf      = $rule_value;
        $this->onlyIfValue = $value;
    }

    /**
     * Set
     * 
     * @param mixed $rule_value
     */
    protected function setRequiredModifier(mixed $rule_value)
    {
        $this->required = $rule_value ?? true;
    }

    /**
     * String to array
     * 
     * @param string $value
     * @param string $separator
     */
    protected function strToArr(string $value, string $separator = ','): array
    {
        return array_map('trim', explode($separator, $value));
    }

    /**
     * Set
     */
    protected function getTimestap(mixed $dt): int
    {
        if (!$dt) {
            if ($this->type == 'datetime') {
                return time();
            }
            $dt = date('Y-m-d 00:00:00');
        }

        if (is_string($dt)) {
            return strtotime($dt);
        }

        if (is_int($dt)) {
            return $dt;
        }

        if ($dt instanceof \Datetime) {
            return $dt->getTimestamp();
        }

        throw new \Exception(sprintf(
            'Date format not recognized in «%s» filter',
            get_class($this)
        ));
    }
}

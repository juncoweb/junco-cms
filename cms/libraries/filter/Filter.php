<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Filter\Filters\FilterInterface;

class Filter
{
    // vars
    protected array  $data      = [];
    protected ?array $files     = null;
    protected array  $filters   = [];
    protected array  $var_names = [];
    protected array  $classes   = [
        // numbers
        'int' => Junco\Filter\Filters\Integer::class,
        'id' => Junco\Filter\Filters\Id::class,
        'float' => Junco\Filter\Filters\Float_::class,

        // string
        'text' => Junco\Filter\Filters\Text::class,
        'multiline' => Junco\Filter\Filters\Multiline::class,
        //
        'url' => Junco\Filter\Filters\Url::class,
        'email' => Junco\Filter\Filters\Email::class,
        'color' => Junco\Filter\Filters\Color::class,
        'uuid' => Junco\Filter\Filters\Uuid::class,
        'slug' => Junco\Filter\Filters\Slug::class,

        // boolean
        'bool' => Junco\Filter\Filters\Boolean::class,

        // date
        'date' => Junco\Filter\Filters\Date::class,
        'time' => Junco\Filter\Filters\Time::class,
        'datetime' => Junco\Filter\Filters\Datetime::class,
        'month' => Junco\Filter\Filters\Month::class,

        // file
        'archive' => Junco\Filter\Filters\Archive::class,
        'file' => Junco\Filter\Filters\File::class,
        'files' => Junco\Filter\Filters\Files::class,
        'image' => Junco\Filter\Filters\Image::class,
        'images' => Junco\Filter\Filters\Images::class,

        // others
        'callback' => Junco\Filter\Filters\Callback::class,
        'json' => Junco\Filter\Filters\Json::class,
        'enum' => Junco\Filter\Filters\Enum::class,
        'none' => Junco\Filter\Filters\None::class,
    ];

    /**
     * Constructor
     * 
     * @param bool $iterate
     */
    protected function __construct(
        protected bool $iterate = false
    ) {}

    /**
     * Groups several filters of the framework
     * 
     * It is a filter_var mask, so it will have similar behaviors
     * 
     * @param int          $type
     * @param string       $var_name
     * @param string|array $rules
     * 
     * @return mixed
     */
    public static function input(int $type, string $var_name, string|array $rules = ''): mixed
    {
        return (new self)
            ->prepare([$var_name => $rules])
            ->execute($type)
            ?->fetch()[$var_name];
    }

    /**
     * Filter a variable
     * 
     * @param mixed        $variable
     * @param string|array $rules
     * 
     * @return mixed
     */
    public static function var(mixed $variable, string|array $rules = ''): mixed
    {
        return (new self)
            ->prepare(['x' => $rules])
            ->execute(['x' => $variable])
            ?->fetch()['x'];
    }

    /**
     * Similar to filter_var_array
     *
     * @param array|int  $data			
     * @param array      $definition
     * @param bool       $iterate     This is NOT the third parameter of filter_input_array
     * 	- In the case of iterating, an array containing the arrays with the
     * 	  key / value pairs of the definition will be returned.
     * 	- This behavior is useful when you want to retrieve values such as the following:
     * 	   key[][a] = value1
     * 	   key[][a] = value2
     * 
     * @return ?array
     */
    public static function all(array|int $data, array $definition, bool $iterate = false): ?array
    {
        return (new self($iterate))
            ->prepare($definition)
            ->execute($data)
            ?->fetch();
    }

    /**
     * Prepare
     * 
     * @param array $definition
     * 
     * @return self
     */
    protected function prepare(array $definition): self
    {
        foreach ($definition as $var_name => $rules) {
            $this->var_names[] = $var_name;
            $this->filters[$var_name] = $this->getFilter($var_name, $rules);
        }

        return $this;
    }

    /**
     * Get
     * 
     * @param string	   $var_name
     * @param string|array $rules
     * 
     * @return FilterInterface
     */
    protected function getFilter(string $var_name, string|array $rules): FilterInterface
    {
        $modifiers = [];
        $filter = null;

        if (!$rules) {
            $rules = [];
        } elseif (is_string($rules)) {
            $rules = $this->parseRules($var_name, $rules);
        }

        foreach ($rules as $rule => $value) {
            if (isset($this->classes[$rule])) {
                if ($filter !== null) {
                    throw new FilterError(sprintf('Rules cannot have more than one filter for the variable «%s»', $var_name));
                }
                $filter = new $this->classes[$rule]($value);
            } else {
                $modifiers[$rule] = $value;
            }
        }

        // If there is no filter, it will be a "none" filter
        $filter ??= new $this->classes['none']();
        $filter->setModifiers($modifiers);

        return $filter;
    }

    /**
     * Parse string rules and return an array.
     * 
     * @param string	   $var_name
     * @param string|array $rules
     * 
     * @return array
     */
    protected function parseRules(string $var_name, string $rules): array
    {
        if (!preg_match_all('#([^|^:]+)(?::([^|]*))?#', $rules, $matches, PREG_SET_ORDER)) {
            throw new FilterError(sprintf('An error occurred when reading the filter rules for the variable «%s»', $var_name));
        }

        $rules = [];
        foreach ($matches as $match) {
            $rules[$match[1]] = $match[2] ?? null; // The null value is very important!
        }

        return $rules;
    }

    /**
     * Execute
     * 
     * @param array|int $data  An associative array (key - value) or filter type constants: FILTER_GET, FILTER_POST...
     * 
     * @return self 
     */
    protected function execute(array|int $data)
    {
        // I get the definition
        $definition = [];
        foreach ($this->var_names as $var_name) {
            $definition[$var_name] = $this->filters[$var_name]->argument;
        }

        // I force the array
        $add_empty = true;
        if ($this->iterate) {
            $this->addForceArrayFlag($definition);
            $add_empty = false;
        }

        // I perform the filtering
        if (is_int($data)) {
            //$partial = filter_input_array($data, $definition, $add_empty);
            $data = $this->retrieveData($data);
        }
        $partial = filter_var_array($this->filterNullValues($data), $definition, $add_empty);

        // I change the array format of the request to the standard format
        if ($this->iterate) {
            if (!$partial) {
                return null;
            }
            foreach (array_keys($partial) as $var_name) {
                foreach ($partial[$var_name] as $index => $value) {
                    $this->data[$index][$var_name] = $value;
                }
            }
        } else {
            $this->data[] = $partial ?: [];
        }

        // callback
        foreach ($this->data as $index => $row) {
            foreach ($this->var_names as $var_name) {
                $value    = $row[$var_name] ?? null;
                $file     = null;
                $altValue = null;

                if ($this->filters[$var_name]->isFile) {
                    $this->files ??= request()?->getUploadedFiles() ?? [];
                    $file = $this->iterate
                        ? ($this->files[$index][$var_name] ?? null)
                        : ($this->files[$var_name] ?? null);

                    // Hack, only for Images
                    if ($this->filters[$var_name]->altValue) {
                        $altValue = $this->iterate
                            ? ($data['__' . $index][$var_name] ?? null)
                            : ($data['__' . $var_name] ?? null);
                    }
                } elseif (empty($value) && $this->filters[$var_name]->orUse) {
                    $value = $row[$this->filters[$var_name]->orUse] ?? null;
                }

                $this->data[$index][$var_name] = $this->filters[$var_name]->filter($value, $file, $altValue);
            }

            // validations
            foreach ($this->var_names as $var_name) {
                if ($this->filters[$var_name]->onlyIf) {
                    if ($this->filterOnlyIf($index, $var_name)) {
                        continue;
                    }
                }
                if ($this->filters[$var_name]->required) {
                    $this->validateRequired($index, $var_name);
                }
            }
        }

        return $this;
    }

    /**
     * Fetch data
     * 
     * @return mixed
     */
    protected function fetch()
    {
        return $this->iterate ? $this->data : $this->data[0];
    }

    /**
     * Add Force Array Flag
     * 
     * @param array &$definition
     */
    protected function addForceArrayFlag(array &$definition)
    {
        foreach ($this->var_names as $var_name) {
            if (!$this->filters[$var_name]->isArray) {
                $definition[$var_name]['flags'] = isset($definition[$var_name]['flags'])
                    ? $definition[$var_name]['flags'] | FILTER_FORCE_ARRAY
                    : FILTER_FORCE_ARRAY;
            }
        }
    }

    /**
     * Retrieve data
     * 
     * @param int $type
     * 
     * @throws FilterError
     * 
     * @return array
     */
    protected function retrieveData(int $type): array
    {
        switch ($type) {
            case GET:
                return request()?->getQueryParams() ?? [];
            case POST:
                return request()?->getParsedBody() ?? [];
        }

        throw new FilterError('The type of data to be filtered is incorrect');
    }

    /**
     * Prevents the "filter_var_array" function from converting null values to ''
     * 
     * @param array $data
     */
    protected function filterNullValues(array $data)
    {
        return array_filter($data, function ($value) {
            return $value !== null;
        });
    }

    /**
     * filter Only If
     * 
     * @param int    $index,
     * @param string $var_name
     * 
     * @return bool
     */
    protected function filterOnlyIf(int $index, string $var_name): bool
    {
        if (empty($this->data[$index][$this->filters[$var_name]->onlyIf]) == $this->filters[$var_name]->onlyIfValue) {
            unset($this->data[$index][$var_name]);
            return true;
        }
        return false;
    }

    /**
     * Required
     * 
     * @param int    $index,
     * @param string $var_name
     * 
     * @throws FilterError
     * @throws FilterException
     */
    protected function validateRequired(int $index, string $var_name): void
    {
        $value = $this->data[$index][$var_name];

        if (
            !$value
            || ($this->filters[$var_name]->isArray && in_array(false, $value)) // partially empty
        ) {
            if ($this->filters[$var_name]->required === 'abort') {
                throw new FilterError(sprintf('The variable «%s» has generated an error in the filter', $var_name));
            }

            $message = $this->getMessage($var_name);
            if ($this->iterate) {
                $message .= sprintf(' (%d)', $index + 1);
            }

            throw new FilterException($message);
        }
    }

    /**
     * Get
     * 
     * @param string $var_name
     * 
     * @return string
     */
    protected function getMessage(string $var_name): string
    {
        if (preg_match('#(?:^|_)(name|title|email|url|key|password)(?:_|$)#', $var_name, $match)) {
            switch ($match[1]) {
                case 'name':
                    return _t('Please, fill in the name.');

                case 'title':
                    return _t('Please, fill in the title.');

                case 'email':
                    return _t('Please, fill in with a valid email.');

                case 'url':
                    return _t('Please, fill in with a valid link.');

                case 'key':
                    return _t('Please, fill in the key.');

                case 'password':
                    return _t('Please, fill in the password.');
            }
        }

        return _t('Please, fill in the required data.');
    }
}

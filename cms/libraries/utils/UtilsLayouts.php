<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class UtilsLayouts
{
    //
    protected array $filters    = [];
    protected array $data       = [];
    protected array $translates = [];

    /**
     * 
     */
    public function data(array $data)
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * 
     */
    public function translates(array $translates)
    {
        $this->translates = array_merge($this->translates, array_map('strtolower', $translates));
    }

    /**
     * Filter
     */
    public function filter(callable $func)
    {
        $this->filters[] = $func;
    }

    /**
     * Date
     */
    public function setDate(?Date $date = null, string $prefix = '')
    {
        if ($date === null) {
            $date = new Date();
        }
        if ($prefix) {
            $prefix .= '_';
        } else {
            $this->translates([
                'date'  => _t('Date'),
                'day'   => _t('Day'),
                'month' => _t('Month'),
                'year'  => _t('Year'),
                'time'  => _t('Time'),
                'hour'  => _t('Hour'),
            ]);
        }

        $this->filters[] = function ($value) use ($prefix, $date) {
            switch ($value) {
                case $prefix . 'date':
                    return $date->format(_t('Y-m-d'));
                case $prefix . 'day':
                    return $date->format('j');
                case $prefix . 'dayname':
                    return $date->dayName;
                case $prefix . 'month':
                    return $date->format('F');
                case $prefix . 'monthname':
                    return $date->monthName;
                case $prefix . 'year':
                    return $date->format('Y');
                case $prefix . 'time':
                    return $date->format('H:i:s');
                case $prefix . 'hour':
                    return $date->format('H:i');
            }
        };
    }

    /**
     * Create
     */
    public function create(string $html)
    {
        return preg_replace_callback('#\{\{\s*(.*?)\s*\}\}#', [$this, 'replace'], $html);
    }

    /**
     * Replace
     */
    protected function replace(array $match)
    {
        $value = strtolower($match[1]);

        // translate
        if (in_array($value, $this->translates)) {
            $value = array_search($value, $this->translates);
        }

        // data
        if (isset($this->data[$value])) {
            return $this->data[$value];
        }

        // filters
        foreach ($this->filters as $filter) {
            $result = $filter($value);
            if ($result !== null) {
                return $result;
            }
        }

        return $match[0];
    }
}

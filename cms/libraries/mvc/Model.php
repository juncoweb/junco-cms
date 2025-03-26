<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Mvc;

use Filter;

class Model
{
    // vars
    protected $data            = [];
    protected $data_array    = [];
    private   $__data        = null;
    private   $__data_array    = null;

    /**
     * Set data
     * 
     * @param array $data
     * 
     * @return static
     */
    final public function setData(?array $data = null): static
    {
        $this->__data = $data;

        return $this;
    }

    /**
     * Filter
     * 
     * @param int   $type
     * @param array $rules
     * 
     * @return void
     */
    final protected function filter(int $type, array $rules): void
    {
        if ($this->__data !== null) {
            $type            = $this->__data;
            $this->__data    = null;
        }

        $this->data = Filter::all($type, $rules);
    }

    /**
     * Set data array
     * 
     * @param array $data_array
     * 
     * @return static
     */
    final public function setDataArray(?array $data_array = null): static
    {
        $this->__data_array = $data_array;

        return $this;
    }

    /**
     * Set data
     * 
     * @param int   $type
     * @param array $rules
     * 
     * @return bool
     */
    final protected function filterArray(int $type, array $rules): bool
    {
        if ($this->__data_array !== null) {
            $type                = $this->__data_array;
            $this->__data_array = null;
        }

        return (bool)$this->data_array = Filter::all($type, $rules, true);
    }

    /**
     * Extract
     * 
     * @param ... $names
     * 
     * @return void
     */
    final public function extract(...$names): void
    {
        foreach ($names as $name) {
            $this->$name = $this->data[$name];
            unset($this->data[$name]);
        }
    }
}

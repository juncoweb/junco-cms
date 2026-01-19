<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Mvc;

use Filter;

class Model
{
    // vars
    protected array  $data         = [];
    protected array  $data_array   = [];
    private   ?array $__data       = null;
    private   ?array $__data_array = null;

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
     * @return array
     */
    final protected function filter(int $type, array $rules): array
    {
        if ($this->__data !== null) {
            $type = $this->__data;
            $this->__data = null;
        }

        return $this->data = Filter::all($type, $rules);
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
     * @return ?array
     */
    final protected function filterArray(int $type, array $rules): ?array
    {
        if ($this->__data_array !== null) {
            $type = $this->__data_array;
            $this->__data_array = null;
        }

        return $this->data_array = Filter::all($type, $rules, true);
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

    /**
     * Slice
     * 
     * @param array  &$data
     * @param string $key
     * 
     * @return mixed
     */
    final public function slice(array &$data, string $key): mixed
    {
        if (!array_key_exists($key, $data)) {
            return null;
        }

        $value = $data[$key];
        unset($data[$key]);

        return $value;
    }

    /**
     * Result
     * 
     * @param int    $statusCode
     * @param string $message
     * @param int    $code
     * 
     * @return Result
     */
    protected function result(int $statusCode = 0, string $message = '', int $code = 0, mixed $data = null): Result
    {
        return new Result($statusCode, $message, $code, $data);
    }

    /**
     * Unprocessable
     * 
     * @param string $message
     * 
     * @return Result
     */
    protected function unprocessable(string $message): Result
    {
        return new Result(422, $message);
    }
}

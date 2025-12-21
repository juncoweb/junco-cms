<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Zoom;

use Closure;

abstract class LogsBase
{
    // vars
    protected $db;
    protected ?int   $notary_id = null;
    protected int    $max_chars = 120;
    protected string $tag       = '<div class="badge badge-regular badge-small">%s: <span class="color-light">%s</span> => %s</div>';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Get
     * 
     * @param string   $scope
     * @param int      $register_id
     * @param ?Closure $fn
     * 
     * @return array
     */
    public abstract function getAll(string $scope, int $register_id, ?Closure $fn = null): array;

    /**
     * Store
     * 
     * @param string $scope
     * @param int    $register_id
     * @param array  $data
     * @param ?array $oldData
     * 
     * @return void
     */
    public abstract function store(string $scope, int $register_id, array $data, ?array $oldData = null): void;

    /**
     * Store All
     * 
     * @param string $scope
     * @param array  $register_id
     * @param array  $newData
     * @param ?array $oldDataList
     * 
     * @return void
     */
    public function storeAll(string $scope, array $register_id, array $newData, array $oldDataList): void
    {
        foreach ($register_id as $id) {
            $this->store($scope, $id, $this->merge($newData, $oldDataList[$id]));
        }
    }

    /**
     * Remove
     * 
     * @param string $scope
     * @param int|array $register_id
     * 
     * @return int
     */
    public abstract function remove(string $scope, array|int $register_id): int;

    /**
     * Get
     */
    protected function getMessage(string $data, ?Closure $fn): string
    {
        $data   = (array)json_decode($data, true);
        $output = [];

        foreach ($data as $key => $value) {
            $oldValue = $this->sanitizeValue($value[0] ?? '');
            $newValue = $this->sanitizeValue($value[1] ?? '');
            $message  = null;

            if ($fn !== null) {
                $message = $fn($key, $oldValue, $newValue);
            }

            $output[] = $message ?? sprintf($this->tag, $key, $oldValue, $newValue);
        }

        return implode($output);
    }

    /**
     * Get
     */
    protected function sanitizeValue(mixed $value): string
    {
        if (!is_string($value) && !is_numeric($value)) {
            return '?';
        }

        if (strlen($value) > $this->max_chars) {
            return '...';
        }

        if (strpos($value, "<") !== false) {
            return htmlentities($value);
        }

        return $value;
    }

    /**
     * Merge
     */
    protected function merge(array $newData, array $oldData): array
    {
        $data = [];

        foreach ($newData as $key => $value) {
            if ($oldData[$key] != $value) {
                $data[$key] = [$oldData[$key], $value];
            }
        }

        return $data;
    }
}

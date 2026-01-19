<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Logger\Adapter;

use Junco\Logger\Enum\LogLevel;
use Junco\Logger\Enum\LogStatus;
use Junco\Utils\Aleatory;

class ArrayAdapter implements AdapterInterface
{
    protected array $storage = [];

    /**
     * Gets all logs.
     *
     * @return array
     */
    public function fetchAll(array $where = []): array
    {
        $rows = $this->storage;
        $where and $this->where($rows, $where);

        return $rows;
    }

    /**
     * Fetch
     *
     * @param string $id
     * 
     * @return array|null
     */
    public function fetch(string $id): array|null
    {
        foreach ($this->fetchAll() as $row) {
            if ($row['id'] == $id) {
                return $row;
            }
        }

        return null;
    }

    /**
     * Stores a log
     *
     * @param LogLevel $level
     * @param string   $message
     * @param array    $context
     * 
     * @return bool
     */
    public function log(LogLevel $level, string $message, array $context): bool
    {
        $id         = Aleatory::uuid();
        $status     = LogStatus::unchecked;
        $created_at = date('Y-m-d H:i:s');

        $this->storage[] = [
            'id'         => $id,
            'level'      => $level,
            'message'    => $message,
            'context'    => $context,
            'created_at' => $created_at,
            'status'     => $status,
        ];

        return true;
    }

    /**
     * Toggle the status of the registers.
     * 
     * @param string[]   $id
     * @param ?LogStatus $status
     * 
     * @return bool
     */
    public function status(array $id, ?LogStatus $status = null): bool
    {
        $rows = $this->fetchAll();

        foreach ($rows as $i => $row) {
            if (in_array($row['id'], $id)) {
                $rows[$i]['status'] = ($status === null)
                    ? $row['status']->toggle()
                    : $status;
            }
        }

        return $this->storeAll($rows);
    }

    /**
     * Delete
     * 
     * @param string[] $id
     *
     * @return bool
     */
    public function deleteMultiple(array $id): bool
    {
        $rows = $this->fetchAll();

        foreach ($rows as $i => $row) {
            if (in_array($row['id'], $id)) {
                unset($rows[$i]);
            }
        }

        return $this->storeAll($rows);
    }

    /**
     * Stores the complete list of logs
     *
     * @param array $rows  All the logs to save.
     *
     * @return bool
     */
    public function storeAll(array $rows = []): bool
    {
        $this->storage = $rows;

        return true;
    }

    /**
     * Where
     */
    public function where(array &$rows, array $where = []): void
    {
        foreach ($rows as $i => $row) {
            foreach ($where as $key => $value) {
                if (!$this->filter($key, $row[$key] ?? null, $value)) {
                    unset($rows[$i]);
                    continue 2;
                }
            }
        }
    }

    /**
     * Filter
     */
    protected function filter(string $key, $value, $cmpValue): bool
    {
        if ($key == 'level') {
            return $value === $cmpValue;
        }

        if ($key == 'status') {
            return $value === $cmpValue;
        }

        if ($key == 'id') {
            return is_array($cmpValue)
                ? !in_array($value, $cmpValue)
                : ($value != $value);
        }

        return true;
    }
}

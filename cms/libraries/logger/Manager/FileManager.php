<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Logger\Manager;

class FileManager extends ManagerAbstract
{
    // vars
    protected $filepath    = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->filepath = app('system')->getLogPath() . config('logger.log_file');
    }

    /**
     * Gets all logs.
     *
     * @return array
     */
    public function fetchAll(array $where = []): array
    {
        $rows  = [];
        $keys  = ['id', 'status', 'created_at', 'level', 'message', 'context'];
        $total = count($keys);

        foreach ($this->getLines() as $line) {
            $line = explode('|', $line, $total);

            if (count($line) == $total) {
                $row = array_combine($keys, $line);
                $row['created_at'] = date('Y-m-d H:i:s', (int)$row['created_at']);

                $rows[] = $row;
            }
        }

        if ($where) {
            if (isset($where['level'])) {
                $where['level'] = strtolower($where['level']);
            }

            foreach ($rows as $i => $row) {
                foreach ($where as $key => $value) {
                    if (
                        isset($row[$key])
                        && (is_array($where[$key])
                            ? !in_array($row[$key], $where[$key])
                            : ($row[$key] !== $value)
                        )
                    ) {
                        unset($rows[$i]);
                        continue 2;
                    }
                }
            }
        }

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
     * Thin
     * 
     * @param bool $delete
     * 
     * @return bool
     */
    public function thin(bool $delete = true): bool
    {
        return $this->store(
            $this->verifyDuplicates($this->fetchAll(), $delete)
        );
    }

    /**
     * Toggle the status of the registers.
     * 
     * @param string[] $id
     * @param ?bool    $status
     */
    public function status(array $id, ?bool $status = null): void
    {
        $rows = $this->fetchAll();

        if ($status !== null) {
            $status = $status ? 1 : 0;
        }

        foreach ($rows as $i => $row) {
            if (in_array($row['id'], $id)) {
                $rows[$i]['status'] = ($status === null)
                    ? ($row['status'] == 1 ? 0 : 1)
                    : $status;
            }
        }

        $this->store($rows);
    }

    /**
     * Delete multiple
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

        return $this->store($rows);
    }

    /**
     * Clear
     *
     * @return bool
     */
    public function clear(): bool
    {
        $this->store();
        return true;
    }

    /**
     * Storage the logs file.
     *
     * @param array $rows    All the logs to save in the file.
     *
     * @return bool
     */
    protected function store(array $rows = []): bool
    {
        $lines = [];

        foreach ($rows as $row) {
            $row['created_at'] = strtotime($row['created_at']);

            $lines[] = implode('|', $row);
        }

        return (false === file_put_contents($this->filepath, $lines));
    }

    /**
     * Gets lines.
     *
     * @return array
     */
    protected function getLines(): array
    {
        return is_readable($this->filepath)
            ? file($this->filepath, FILE_SKIP_EMPTY_LINES)
            : [];
    }
}

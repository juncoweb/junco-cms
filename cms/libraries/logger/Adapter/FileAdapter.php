<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Logger\Adapter;

use Junco\Logger\Enum\LogLevel;
use Junco\Logger\Enum\LogStatus;

class FileAdapter implements AdapterInterface
{
    // vars
    protected $dirpath  = '';
    protected $filepath = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dirpath  = app('system')->getLogPath();
        $this->filepath = $this->dirpath . (config('logger.log_file') ?: 'error_log');
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
                $rows[] = array_combine($keys, $line);
            }
        }

        // where
        $where and $this->where($rows, $where);

        //
        foreach ($rows as $i => $row) {
            if (is_numeric($row['created_at'])) {
                $rows[$i]['created_at'] = date('Y-m-d H:i:s', (int)$row['created_at']); // @deprecated in v14.8
            }
            $rows[$i]['level']   = $levels[$row['level']] ??= LogLevel::get($row['level']);
            $rows[$i]['status']  = $statuses[$row['status']] ??= LogStatus::get($row['status']);
            $rows[$i]['context'] = json_decode($row['context'], true);
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
        $lines = is_file($this->filepath)
            ? file($this->filepath)
            : [];

        $count      = count($lines);
        $id         = ($count ? (int)$lines[$count - 1] : 0) + 1;
        $status     = LogStatus::unchecked->name;
        $created_at = date('Y-m-d H:i:s');
        $message    = filter_var($message, FILTER_SANITIZE_SPECIAL_CHARS);
        $context    = json_encode($context, JSON_UNESCAPED_SLASHES);

        is_dir($this->dirpath)
            or mkdir($this->dirpath, SYSTEM_MKDIR_MODE, true);

        return error_log(implode('|', [
            'id'      => $id,
            'status'  => $status,
            'created' => $created_at,
            'level'   => $level->name,
            'message' => $message,
            'context' => $context,
        ]) . PHP_EOL, 3, $this->filepath);
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
        $lines = [];

        foreach ($rows as $row) {
            $row['level']      = $row['level']->name;
            $row['status']     = $row['status']->name;
            $row['created_at'] = $row['created_at'];
            $row['context']    = json_encode($row['context'], JSON_UNESCAPED_SLASHES);

            $lines[] = implode('|', $row);
        }

        return (false === file_put_contents($this->filepath, implode(PHP_EOL, $lines)));
    }

    /**
     * Where
     */
    public function where(array &$rows, array $where = []): void
    {
        foreach ($rows as $i => $row) {
            foreach ($where as $key => $value) {
                if (!$this->filter($key, $row[$key], $value)) {
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
            return $value == $cmpValue->name;
        }

        if ($key == 'status') {
            return $value == $cmpValue->value;
        }

        if ($key == 'id') {
            return is_array($cmpValue)
                ? in_array($value, $cmpValue)
                : $value == $cmpValue;
        }

        return true;
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

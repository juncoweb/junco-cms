<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Logger;

use Junco\Logger\Enum\LogStatus;
use Junco\Logger\Adapter\AdapterInterface;
use Junco\Session\UserAgent;

class LoggerManager
{
    protected AdapterInterface $adapter;

    /**
     * Constructor
     */
    public function __construct(?AdapterInterface $adapter = null)
    {
        $this->adapter = $adapter ?? app('logger')->getAdapter();
    }

    /**
     * Gets all logs.
     *
     * @return array
     */
    public function fetchAll(array $where = []): array
    {
        return $this->adapter->fetchAll($where);
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
        return $this->adapter->fetch($id);
    }

    /**
     * Toggle the status of the registers.
     * 
     * @param string[]   $id
     * @param ?LogStatus $status
     */
    public function status(array $id, ?LogStatus $status = null): void
    {
        $this->adapter->status($id, $status);
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
        return $this->adapter->deleteMultiple($id);
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
        return $this->adapter->storeAll(
            $this->verifyDuplicates($this->adapter->fetchAll(), $delete)
        );
    }

    /**
     * Clear
     *
     * @return bool
     */
    public function clear(): bool
    {
        return $this->adapter->storeAll();
    }

    /**
     * Get
     * 
     * @param array $id
     * 
     * @return array
     */
    public function getReports(array $id = []): array
    {
        $reports = [];
        $rows = $this->verifyDuplicates(
            $this->adapter->fetchAll($id ? ['id' => $id] : [])
        );

        foreach ($rows as $row) {
            $this->extractFromContext($row, ['file', 'line', 'backtrace']);

            $reports[] = [
                'level'      => $row['level']->name,
                'message'    => $this->shortenFile($row['message']),
                'file'       => $row['file'],
                'line'       => $row['line'],
                'backtrace'  => $row['backtrace'],
                'created_at' => $row['created_at'],
            ];
        }

        return $reports;
    }

    /**
     * Compact
     * 
     * @param array $data
     * @param array $extract_from_context    The keys to be extracted.
     */
    public function compact(array $data, array $extract_from_context = []): array
    {
        $data['message'] = $this->shortenFile($data['message']);

        $extract_from_context
            and $this->extractFromContext($data, $extract_from_context);

        return $data;
    }

    /**
     * Extracts data from the context.
     * 
     * @param array $data
     * @param array $extract    The keys to be extracted.
     */
    protected function extractFromContext(array &$data, array $extract): void
    {
        foreach ($extract as $key) {
            if (isset($data['context'][$key])) {
                $data[$key] = $this->shortenFile($data['context'][$key]);
                unset($data['context'][$key]);
            } else {
                $data[$key] = '';
            }
        }

        if (!empty($data['line'])) {
            $data['file'] .= ':' . $data['line'];
        }

        if (!empty($data['backtrace'])) {
            $data['backtrace'] = explode("\n", $data['backtrace']);
        }

        if ($value = $data['context']['user_agent'] ?? null) {
            $data['context']['user_agent'] = $this->shortenUserAgent($value);
        }
    }

    /**
     * Get
     */
    protected function shortenFile(string $file): string
    {
        return str_replace(SYSTEM_ABSPATH, '', $file);
    }

    /**
     * Get
     */
    protected function shortenUserAgent(string $user_agent): string
    {
        $ua = new UserAgent($user_agent);

        return implode('/', [
            $ua->getPlatform(),
            $ua->getBrowser(),
            $ua->getVersion()
        ]);
    }

    /**
     * Verify
     * 
     * @param array $rows
     * @param bool  $delete
     * 
     * @return array
     */
    public function verifyDuplicates(array $rows, bool $delete = true): array
    {
        $keys = [];

        foreach ($rows as $i => $row) {
            $key = $this->getInternalKey($row);

            if (!in_array($key, $keys)) {
                $keys[] = $key;
            } elseif ($delete) {
                unset($rows[$i]);
            } else {
                $rows[$i]['status'] = LogStatus::repeated;
            }
        }

        return $rows;
    }

    /**
     * Get
     * 
     * @param array $row
     * 
     * @return string
     */
    protected function getInternalKey(array $row): string
    {
        if (
            isset($row['context']['file'])
            && isset($row['context']['line'])
        ) {
            return $row['context']['file'] . ':' . $row['context']['line'];
        }

        return $row['message'];
    }
}

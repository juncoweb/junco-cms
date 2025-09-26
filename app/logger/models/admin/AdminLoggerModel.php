<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Logger\LoggerManager;

class AdminLoggerModel extends Model
{
    // vars
    protected $manager;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->manager = new LoggerManager;
    }

    /**
     * Get
     */
    public function getListData()
    {
        $data = $this->filter(POST, [
            'status' => 'int',
            'level' => 'id|max:8',
        ]);

        // vars
        $levels = [
            _t('All levels'),
            'EMERGENCY',
            'ALERT',
            'CRITICAL',
            'ERROR',
            'WARNING',
            'NOTICE',
            'INFO',
            'DEBUG'
        ];

        $where = [];
        if ($data['level']) {
            $where['level'] = $levels[$data['level']];
        }

        if ($data['status']) {
            $where['status'] = $data['status'] - 1;
        }

        $rows = $this->manager->fetchAll($where);
        if ($rows) {
            $rows = array_reverse($rows);
        }

        // query
        $pagi = new Pagination();
        $pagi->slice($rows);

        $rows = [];
        foreach ($pagi->fetchAll() as $row) {
            $this->manager->extractFromContext($row, ['file', 'line']);

            $row['created_at']    = new Date($row['created_at']);
            if ($row['line']) {
                $row['file'] .= ':' . $row['line'];
            }

            $rows[] = $row;
        }

        return $data + [
            'levels' => $levels,
            'rows' => $rows,
            'pagi' => $pagi
        ];
    }

    /**
     * Get
     */
    public function getShowData()
    {
        $input = $this->filter(POST, ['id' => 'id|array:first|required:abort']);

        // vars
        $data = $this->manager->fetch($input['id']) or abort();
        $this->manager->extractFromContext($data, ['file', 'line', 'backtrace']);


        $data['created_at']    = new Date($data['created_at']);
        $data['backtrace'] = explode("\n", $data['backtrace']);

        if ($data['line']) {
            $data['file'] .= ':' . $data['line'];
        }

        return $data;
    }

    /**
     * Get
     */
    public function getConfirmDeleteData()
    {
        return $this->filter(POST, ['id' => 'id|array|required:abort']);
    }

    /**
     * Get
     */
    public function getConfirmReportData()
    {
        $data = $this->filter(POST, ['id' => 'id|array']);

        return $data + [
            'total'  => count($data['id']),
            'values' => $data,
        ];
    }
}

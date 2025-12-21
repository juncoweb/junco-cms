<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Logger\Enum\LogStatus;
use Junco\Logger\Enum\LogLevel;
use Junco\Logger\LoggerManager;

class AdminLoggerModel extends Model
{
    /**
     * Get
     */
    public function getIndexData()
    {
        return [
            'statuses' => LogStatus::getActives()
        ];
    }

    /**
     * Get
     */
    public function getListData()
    {
        $data = $this->filter(POST, [
            'status' => 'enum:logger.log_status',
            'level'  => 'enum:logger.log_level',
        ]);

        //trigger_error('test', E_USER_DEPRECATED);
        $where = [];
        if ($data['level']) {
            $where['level'] = $data['level'];
        }

        if ($data['status']) {
            $where['status'] = $data['status'];
        }

        $manager = new LoggerManager;
        $rows    = $manager->fetchAll($where);
        if ($rows) {
            $rows = array_reverse($rows);
        }

        // query
        $pagi = new Pagination();
        $pagi->slice($rows);

        $rows = [];
        foreach ($pagi->fetchAll() as $row) {
            $row['level']      = $row['level']->title();
            $row['status']     = $row['status']->fetch();
            $row['created_at'] = new Date($row['created_at']);

            $rows[] = $manager->compact($row, ['file', 'line']);
        }

        return [
            'level'    => $data['level']?->name,
            'status'   => $data['status']?->name,
            'levels'   => LogLevel::getList(['' => _t('All levels')]),
            'statuses' => LogStatus::getList(['' => _t('All statuses')]),
            'rows'     => $rows,
            'pagi'     => $pagi
        ];
    }

    /**
     * Get
     */
    public function getShowData()
    {
        $input = $this->filter(POST, ['id' => 'id|array:first|required:abort']);

        //
        $manager       = new LoggerManager;
        $data          = $manager->fetch($input['id']) or abort();
        $data['level'] = $data['level']->title();

        return $manager->compact($data, ['file', 'line', 'backtrace']);
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

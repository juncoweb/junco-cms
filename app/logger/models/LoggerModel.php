<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Http\Client;
use Junco\Logger\LoggerManager;

class LoggerModel extends Model
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
     * Status
     */
    public function status()
    {
        $data = $this->filter(POST, [
            'id' => 'id|array|required:abort',
            'status' => 'enum:logger.log_status'
        ]);

        $this->manager->status($data['id'], $data['status']);
    }

    /**
     * Delete
     */
    public function delete()
    {
        $data = $this->filter(POST, ['id' => 'id|array|required:abort']);

        $this->manager->deleteMultiple($data['id']);
    }

    /**
     * Thin
     */
    public function thin()
    {
        $data = $this->filter(POST, ['delete' => 'bool']);

        $this->manager->thin($data['delete']);
    }

    /**
     * Clean
     */
    public function clean()
    {
        $this->manager->clear();
    }

    /**
     * Get
     */
    public function report()
    {
        $input = $this->filter(POST, [
            'id'      => 'id|array',
            'message' => '',
        ]);

        if (strlen($input['message']) > 600) {
            return $this->unprocessable(_t('The text is too long.'));
        }

        $reports = $this->manager->getReports($input['id']);

        if (!$reports) {
            return $this->unprocessable(_t('Please, select at least one element.'));
        }

        $data = [
            'php_version'    => PHP_VERSION,
            'php_os'         => PHP_OS,
            'php_os_family'  => PHP_OS_FAMILY,
            'system_version' => $this->getSystemVersion(),
            'message'        => $input['message'],
            'reports'        => json_encode($reports)
        ];

        $report_url = config('logger.report_url');
        $code = (string)(new Client)
            ->post($report_url, ['data' => $data])
            ->getBody();

        if (!$code) {
            return $this->unprocessable(_t('Error! the task has not been realized.'));
        }
    }

    /**
     * Get
     * 
     * @return string
     */
    protected function getSystemVersion(): string
    {
        return db()->query("
		SELECT extension_version
		FROM `#__extensions`
		WHERE extension_alias = 'system'")->fetchColumn();
    }
}

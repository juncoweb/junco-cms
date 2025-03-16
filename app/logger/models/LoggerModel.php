<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
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
		// data
		$this->filter(POST, ['id' => 'id|array|required:abort']);

		//
		$this->manager->status($this->data['id']);
	}

	/**
	 * Delete
	 */
	public function delete()
	{
		// data
		$this->filter(POST, ['id' => 'id|array|required:abort']);

		//
		$this->manager->deleteMultiple($this->data['id']);
	}

	/**
	 * Thin
	 */
	public function thin()
	{
		// data
		$this->filter(POST, ['delete' => 'bool']);

		//
		$this->manager->thin($this->data['delete']);
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
		// data
		$this->filter(POST, [
			'id' 		=> 'id|array',
			'message'	=> '',
		]);

		if (strlen($this->data['message']) > 600) {
			throw new Exception(_t('The text is too long.'));
		}

		$reports = $this->manager->getReports($this->data['id']);

		if (!$reports) {
			throw new Exception(_t('Please, select at least one element.'));
		}

		$data = [
			'php_version'		=> PHP_VERSION,
			'php_os'			=> PHP_OS,
			'php_os_family'		=> PHP_OS_FAMILY,
			'system_version'	=> $this->getSystemVersion(),
			'message'			=> $this->data['message'],
			'reports'			=> json_encode($reports)
		];

		$report_url = config('logger.report_url');
		$code = (string)(new Client)
			->post($report_url, ['data' => $data])
			->getBody();

		if (!$code) {
			throw new Exception(_t('Error! the task has not been realized.'));
		}
	}

	/**
	 * Get
	 * 
	 * @return string
	 */
	protected function getSystemVersion(): string
	{
		return db()->safeFind("
		SELECT extension_version
		FROM `#__extensions`
		WHERE extension_alias = 'system'")->fetchColumn();
	}
}

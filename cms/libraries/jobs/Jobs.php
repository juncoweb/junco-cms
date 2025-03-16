<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Jobs;

use Junco\Jobs\Driver\DatabaseDriver;
use Junco\Jobs\Driver\DriverInterface;
use Exception;
use Throwable;

class Jobs
{
	// vars
	protected DriverInterface $driver;

	/**
	 * Constructor
	 */
	public function __construct(string $driver = '')
	{
		$this->driver = $this->getDriver($driver);
	}

	/**
	 * Push a new job onto the queue.
	 * 
	 * @param JobInterface $job
	 * @param string       $queue
	 */
	public function push(JobInterface $job, string $queue = ''): void
	{
		$this->driver->push($job, $queue);
	}

	/**
	 * Run
	 */
	public function run(string $queue = ''): void
	{
		while ($data = $this->pop($queue)) {
			try {
				$job = unserialize($data['job_payload']);

				if (!$job) {
					throw new Exception('Unable to deserialize job.');
				}

				$this->terminate($data, $job->handle());
			} catch (Throwable $e) {
				$this->errorHandler($e, $data);
			}
		}
	}

	/**
	 * Get
	 * 
	 * @param string $driver
	 * 
	 * @return DriverInterface
	 */
	protected function getDriver(string $driver): DriverInterface
	{
		return match ($driver) {
			default => new DatabaseDriver()
		};
	}

	/**
	 * Pop the next job off of the queue.
	 * 
	 * @param string $queue
	 * 
	 * @return ?array
	 */
	protected function pop(string $queue = ''): ?array
	{
		return $this->driver->pop($queue);
	}

	/**
	 * Update the status of the job.
	 * 
	 * @param array $data
	 * @param bool  $status
	 */
	protected function terminate(array $data, bool $status): void
	{
		$this->driver->terminate($data, $status);
	}

	/**
	 * Error handler
	 * 
	 * @param Throwable $error
	 * @param array     $data
	 */
	protected function errorHandler(Throwable $error, array $data): void
	{
		$job_id			= $data['id'] ?? 0;
		$job_uuid		= $data['job_uuid'] ?? '';
		$job_queue		= $data['job_queue'];
		$job_payload	= $data['job_payload'];
		$job_error		= $error->__toString();

		db()->safeExec("
		INSERT INTO `#__jobs_failures` (job_id, job_uuid, job_queue, job_payload, job_error)
		VALUES (?, ?, ?, ?, ?)", $job_id, $job_uuid, $job_queue, $job_payload, $job_error);
	}
}

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Jobs\Driver;

use Junco\Jobs\JobInterface;

class DatabaseDriver implements DriverInterface
{
    // vars
    protected $db;
    protected string $default_queue;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
        $this->default_queue = 'default';
    }

    /**
     * Push a new job onto the queue.
     * 
     * @param JobInterface $job
     * @param string       $queue
     */
    public function push(JobInterface $job, string $queue = ''): void
    {
        if (!$queue) {
            $queue = $this->default_queue;
        }
        $job_payload = serialize($job);
        $delay = $job->delay ?? 0;

        $this->db->exec("
		INSERT INTO `#__jobs` (job_queue, job_payload, available_at)
		VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? SECOND))", $queue, $job_payload, $delay);
    }

    /**
     * Pop the next job off of the queue.
     * 
     * @param string $queue
     * 
     * @return ?array
     */
    public function pop(string $queue = ''): ?array
    {
        // query
        if ($queue) {
            $this->db->where("job_queue = ?", $queue);
        }
        $this->db->where("available_at < NOW()");
        $this->db->where("reserved_at IS NULL");

        $data = $this->db->query("
		SELECT
		 id ,
		 job_queue ,
		 job_payload
		FROM `#__jobs`
		[WHERE]
		ORDER BY available_at DESC
		LIMIT 1", $queue ?: $this->default_queue)->fetch();

        if ($data) {
            $this->db->exec("UPDATE `#__jobs` SET num_attempts = num_attempts + 1, reserved_at = NOW() WHERE id = ?", $data['id']);
        }

        return $data ?: null;
    }

    /**
     * Update the status of the job.
     * 
     * @param array $data
     * @param bool  $status
     */
    public function terminate(array $data, bool $status): void
    {
        if ($status) {
            $this->db->exec("DELETE FROM `#__jobs` WHERE id = ?", $data['id']);
        } else {
            $this->db->exec("UPDATE `#__jobs` SET reserved_at = NULL WHERE id = ?", $data['id']);
        }
    }
}

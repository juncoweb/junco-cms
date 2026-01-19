<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Jobs\Driver;

use Junco\Jobs\JobInterface;

interface DriverInterface
{
    /**
     * Push a new job onto the queue.
     * 
     * @param JobInterface $job
     * @param string       $queue
     */
    public function push(JobInterface $job, string $queue = ''): void;

    /**
     * Pop the next job off of the queue.
     * 
     * @param string $queue
     * 
     * @return ?array
     */
    public function pop(string $queue = ''): ?array;

    /**
     * Update the status of the job.
     * 
     * @param array $data
     * @param bool  $status
     */
    public function terminate(array $data, bool $status): void;
}

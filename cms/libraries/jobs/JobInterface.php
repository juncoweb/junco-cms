<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Jobs;

interface JobInterface
{
    /**
     * Execute the job.
     */
    public function handle(): bool;
}

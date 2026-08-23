<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Notifications\Job;

use Junco\Jobs\JobInterface;
use Junco\Notifications\NotifiableInterface;
use Junco\Notifications\NotificationInterface;

class NotificationsJob implements JobInterface
{
    /**
     * Constructor
     */
    public function __construct(
        protected array|NotifiableInterface $notifiables,
        protected NotificationInterface $notification
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): bool
    {
        app('notifications')->sendNow($this->notifiables, $this->notification);

        return true;
    }
}

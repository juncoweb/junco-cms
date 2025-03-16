<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Notifications\Job;

use Junco\Jobs\JobInterface;
use Junco\Notifications\NotifiableInterface;
use Junco\Notifications\NotificationInterface;

class NotificationsJob implements JobInterface
{
	// vars
	protected $notifiables;
	protected $notification;

	/**
	 * Constructor
	 */
	public function __construct(array|NotifiableInterface $notifiables, NotificationInterface $notification)
	{
		$this->notifiables = $notifiables;
		$this->notification = $notification;
	}

	/**
	 * Execute the job.
	 */
	public function handle(): bool
	{
		app('notifications')->sendNow($this->notifiables, $this->notification);

		return true;
	}
}

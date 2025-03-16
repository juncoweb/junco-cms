<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Notifications\NotifiableInterface;
use Junco\Notifications\NotificationInterface;
use Junco\Notifications\Channel\DatabaseChannel;
use Junco\Notifications\Channel\EmailChannel;
use Junco\Notifications\Job\NotificationsJob;
use Junco\Jobs\Jobs;

class Notifications
{
	// vars
	protected array $channels = [];
	protected       $jobs = null;

	/**
	 * Costruct
	 */
	public function __construct()
	{
		if (config('notifications.use_background')) {
			$this->jobs = new Jobs();
		}
	}

	/**
	 * Send
	 */
	public function send(array|NotifiableInterface $notifiables, NotificationInterface $notification)
	{
		if ($this->jobs) {
			$this->jobs->push(new NotificationsJob($notifiables, $notification), $notification->queue ?? '');
		} else {
			$this->sendNow($notifiables, $notification);
		}
	}

	/**
	 * Send
	 */
	public function sendNow(array|NotifiableInterface $notifiables, NotificationInterface $notification)
	{
		if (!is_array($notifiables)) {
			$notifiables = [$notifiables];
		}
		$viaChannels = $notification->via();

		if ($viaChannels) {
			$language = $notification->getLanguage();

			if ($language) {
				//app('language')->getTranslator()->setLanguage($language);
			}

			foreach ($viaChannels as $viaChannel) {
				$channel = $this->getChannel($viaChannel);

				if ($channel) {
					foreach ($notifiables as $notifiable) {
						$channel->send($notifiable, $notification);
					}
				}
			}
		}
	}

	/**
	 * Get channel
	 */
	protected function getChannel(string $channel)
	{
		return ($this->channels[$channel] ??= match ($channel) {
			'database' => new DatabaseChannel(),
			'email' => new EmailChannel(),
			default => null
		});
	}
}

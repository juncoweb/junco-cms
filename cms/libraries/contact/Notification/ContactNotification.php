<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Contact\Notification;

use Junco\Notifications\Notification;
use Utils;

class ContactNotification extends Notification
{
	// vars
	protected string $name;
	protected string $email;
	protected string $message;

	/**
	 * Constructor
	 */
	public function __construct(array $data)
	{
		$this->id		= $data['contact_id'];
		$this->name		= $data['contact_name'];
		$this->email	= $data['contact_email'];
		$this->message	= $data['contact_message'];
	}

	/**
	 * Returns the necessary data for Database channel.
	 */
	public function toDatabase()
	{
		return sprintf(
			_t('You have a message from %s: %s'),
			'<a href="mailto:' . $this->email . '">' . $this->name . '</a>',
			Utils::cutText($this->message)
		);
	}

	/**
	 * Returns the necessary data for Email channel.
	 */
	public function toEmail()
	{
		$from = '<a href="mailto:' . $this->email . '">' . $this->name . '</a>';
		$message = sprintf(_t('You have a message from %s: %s'), $from, $this->message);

		return [
			'subject' => _t('Contact'),
			'message_html' => $message,
			'message_plain'	=> true,
		];
	}
}

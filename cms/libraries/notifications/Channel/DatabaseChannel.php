<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Notifications\Channel;

use Junco\Notifications\NotifiableInterface;
use Junco\Notifications\NotificationInterface;

class DatabaseChannel implements ChannelInterface
{
    // vars
    protected $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Send
     */
    public function send(NotifiableInterface $notifiable, NotificationInterface $notification)
    {
        $user_id = $notifiable->getId();
        $type    = $notification->getType();
        $id      = $notification->getId();
        $message = $notification->toDatabase();

        $this->db->exec("INSERT INTO `#__notifications` (user_id, notification_type, notification_id, notification_message) 
		VALUES (?, ?, ?, ?)", $user_id, $type, $id, $message);
    }
}

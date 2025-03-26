<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Notifications;

abstract class Notifiable implements NotifiableInterface
{
    // vars
    private $sender = null;

    /**
     * Notify
     */
    public function notify(NotificationInterface $notification)
    {
        $this->sender ??= app('notifications');
        $this->sender->send($this, $notification);
    }
}

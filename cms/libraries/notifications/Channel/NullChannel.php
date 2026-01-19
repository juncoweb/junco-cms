<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Notifications\Channel;

use Junco\Notifications\NotifiableInterface;
use Junco\Notifications\NotificationInterface;

class NullChannel implements ChannelInterface
{
    /**
     * Send
     */
    public function send(NotifiableInterface $notifiable, NotificationInterface $notification)
    {
        return null;
    }
}

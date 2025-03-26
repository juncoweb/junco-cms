<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Notifications\Channel;

use Junco\Notifications\NotifiableInterface;
use Junco\Notifications\NotificationInterface;
use \Email;

class EmailChannel implements ChannelInterface
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
        $to_email = $notifiable->getEmail();
        $data = $notification->toEmail();

        if (empty($data['subject'])) {
            $data['subject'] = config('site.name');
        }
        if (empty($data['message_html'])) {
            $data['message_html'] = '';
        }
        if (!isset($data['message_plain'])) {
            $data['message_plain'] = false;
        }

        // mail
        $email = new Email();
        $email->to($to_email);
        $email->subject($data['subject']);
        $email->message($data['message_html'], $data['message_plain']);

        return $email->send();
    }
}

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class EmailModel extends Model
{
    /**
     * Send
     */
    public function send()
    {
        // data
        $this->filter(POST, [
            'email_to'        => 'email|required',
            'email_subject'    => 'text|required',
            'email_message'    => '',
        ]);

        // extract
        extract($this->data);

        // email
        $email = new Email();
        $email->to($email_to);
        $email->subject($email_subject);
        $email->message($email_message, Email::MESSAGE_ALTER_PLAIN);
        $result = $email->send();

        if (!$result) {
            throw new Exception('Error!');
        }
    }
}

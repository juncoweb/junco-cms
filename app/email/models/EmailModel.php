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
            'email_to'      => 'email|required',
            'email_subject' => 'text|required',
            'email_message' => '',
        ]);

        // email
        $email = new Email();
        $email->to($this->data['email_to']);
        $email->subject($this->data['email_subject']);
        $email->message($this->data['email_message'], Email::MESSAGE_ALTER_PLAIN);
        $result = $email->send();

        if (!$result) {
            return $this->unprocessable('Error!');
        }
    }
}

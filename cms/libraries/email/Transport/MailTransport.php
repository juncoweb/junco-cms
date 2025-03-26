<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Email\Transport;

class MailTransport extends TransportAbstract
{
    /**
     * Send
     * 
     * @param Email  $Email
     * 
     * @return bool
     */
    public function send($Email): bool
    {
        $encode  = !ini_get('mbstring.func_overload');
        $to      = $Email->getBatch('to');
        $subject = $Email->getSubject($encode);
        $header  = $Email->getHeader();
        $body    = $Email->getBody();

        if ($this->debug) {
            $this->debug_log[] = '-to: ' . $to;
            $this->debug_log[] = '-encode: ' . ($encode ? 'Yes' : 'No');
            $this->debug_log[] = '-subject: ' . $subject;
            $this->debug_log[] = '';
            $this->debug_log[] = $header . $body;
        }

        return mail($to, $subject, $body, $header);
    }
}

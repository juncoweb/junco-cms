<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class EmailDebugModel extends Model
{
    /**
     * 
     */
    public function take()
    {
        $this->filter(POST, [
            'transport'        => 'text',
            'subject'        => 'text',
            'to'            => 'text',
            'message_type'    => 'text',
            'message_plain' => 'text',
            'message_html'    => '',
        ]);

        if (
            $this->data['transport'] != 'null'
            && app('system')->isDemo()
        ) {
            return [
                'code' => _t('This task is not allowed in demos.'),
                'debug' => _t('This task is not allowed in demos.')
            ];
        }

        return [
            'code' => $this->getCode(),
            'debug' => $this->getDebugOutput()
        ];
    }

    /**
     * Get
     */
    protected function getCode()
    {
        $lines = [];

        if ($this->data['transport']) {
            $lines[] = "//";
            $transport = $this->getTransportClass($this->data['transport']);
            $lines[] = "\$transport = $transport;";

            if ($this->data['transport'] == 'smtp') {
                $lines[] = "\$transport->smtp_host = \$smtp_host;";
                $lines[] = "\$transport->smtp_port = \$smtp_port;";
                $lines[] = "\$transport->smtp_timeout = \$smtp_timeout;";
                $lines[] = "\$transport->smtp_secure = \$smtp_secure;";
                $lines[] = "\$transport->smtp_auth = \$smtp_auth;";
                $lines[] = "\$transport->smtp_user = \$smtp_user;";
                $lines[] = "\$transport->smtp_pwd = \$smtp_pwd;";
            }
            $lines[] = "//";
            $lines[] = "\$email = new Email(\$transport);";
        } else {
            $lines[] = "\$email = new Email;";
        }

        $lines[] = "\$email->to(\$to)";
        $lines[] = "\$email->subject(\$subject)";
        $lines[] = $this->getMessageCode();
        $lines[] = "\$email->send();";

        return '<textarea class="input-field" style="height: 1200px;">'
            . implode(PHP_EOL, $lines)
            . '</textarea>';
    }

    /**
     * Get
     */
    protected function getMessageCode()
    {
        switch ($this->data['message_type']) {
            case 'html':
                return "\$email->message(\$message_html, Email::MESSAGE_IS_HTML);";
            case 'plain':
                return "\$email->message(\$message_plain, Email::MESSAGE_IS_PLAIN);";
            case 'html+plain':
                return "\$email->message(\$message_html, \$message_plain);";
            case 'html+to-plain':
                return "\$email->message(\$message_html, Email::MESSAGE_ALTER_PLAIN);";
        }
    }

    /**
     * Get
     */
    protected function getTransportClass($transport)
    {
        switch ($transport) {
            case 'null':
                return 'new Junco\Email\Transport\NullTransport';
            case 'mail':
                return 'new Junco\Email\Transport\MailTransport';
            case 'smtp':
                return 'new Junco\Email\Transport\SmtpTransport';
        }
    }

    /**
     * Get
     */
    protected function getDebugOutput()
    {
        $transport = $this->getTransport($this->data['transport']);
        $transport->debug();

        $email = new Email($transport);
        $email->to($this->data['to']);
        $email->subject($this->data['subject']);

        switch ($this->data['message_type']) {
            case 'html':
                $email->message($this->data['message_html'], Email::MESSAGE_IS_HTML);
                break;

            case 'plain':
                $email->message($this->data['message_plain'], Email::MESSAGE_IS_PLAIN);
                break;

            case 'html+plain':
                $email->message($this->data['message_html'], $this->data['message_plain']);
                break;

            case 'html+to-plain':
                $email->message($this->data['message_html'], Email::MESSAGE_ALTER_PLAIN);
                break;
        }
        $email->send();

        return '<textarea class="input-field" style="height: 1200px;">'
            . htmlentities($transport->debugOutput())
            . '</textarea>';
    }

    /**
     * Get
     */
    protected function getTransport($transport)
    {
        if (!$transport) {
            $transport = config('email.transport');
        }

        switch ($transport) {
            case 'null':
                return new Junco\Email\Transport\NullTransport();
            case 'mail':
                return new Junco\Email\Transport\MailTransport();
            case 'smtp':
                return new Junco\Email\Transport\SmtpTransport();
        }
    }
}

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class EmailDebugModel extends Model
{
    /**
     * 
     */
    public function take(): array
    {
        $data = $this->filter(POST, [
            'transport'     => 'text',
            'subject'       => 'text',
            'to'            => 'text',
            'message_type'  => 'text',
            'message_plain' => 'text',
            'message_html'  => '',
        ]);

        if (!$this->isAvailable($data['transport'])) {
            return [
                'code' => _t('This task is not allowed in demos.'),
                'debug' => _t('This task is not allowed in demos.')
            ];
        }

        return [
            'code' => $this->getCode($data['transport'], $data['message_type']),
            'debug' => $this->getDebugOutput(
                $data['transport'],
                $data['to'],
                $data['subject'],
                $data['message_type'],
                $data['message_html'],
                $data['message_plain']
            )
        ];
    }

    /**
     * 
     */
    public function isAvailable(string $transport): bool
    {
        return $transport == 'null' || !app('system')->isDemo();
    }

    /**
     * Get
     */
    protected function getCode(string $transport, string $message_type): string
    {
        $lines = [];

        if ($transport) {
            $lines[] = "//";
            $lines[] = "\$transport = " . $this->getTransportClass($transport) . ";";

            if ($transport == 'smtp') {
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
        $lines[] = $this->getMessageCode($message_type);
        $lines[] = "\$email->send();";

        return '<textarea class="input-field" style="height: 1200px;">'
            . implode(PHP_EOL, $lines)
            . '</textarea>';
    }

    /**
     * Get
     */
    protected function getMessageCode(string $message_type): string
    {
        switch ($message_type) {
            case 'html':
                return "\$email->message(\$message_html, Email::MESSAGE_IS_HTML);";
            case 'plain':
                return "\$email->message(\$message_plain, Email::MESSAGE_IS_PLAIN);";
            case 'html+plain':
                return "\$email->message(\$message_html, \$message_plain);";
            case 'html+to-plain':
                return "\$email->message(\$message_html, Email::MESSAGE_ALTER_PLAIN);";
        }

        return '?';
    }

    /**
     * Get
     */
    protected function getTransportClass(string $transport): string
    {
        switch ($transport) {
            case 'null':
                return 'new Junco\Email\Transport\NullTransport';
            case 'mail':
                return 'new Junco\Email\Transport\MailTransport';
            case 'smtp':
                return 'new Junco\Email\Transport\SmtpTransport';
        }

        return '?';
    }

    /**
     * Get
     */
    protected function getDebugOutput(
        string $transport,
        string $to,
        string $subject,
        string $message_type,
        string $message_html,
        string $message_plain
    ): string {
        $transport = $this->getTransport($transport);
        $transport->debug();

        $email = new Email($transport);
        $email->to($to);
        $email->subject($subject);

        switch ($message_type) {
            case 'html':
                $email->message($message_html, Email::MESSAGE_IS_HTML);
                break;

            case 'plain':
                $email->message($message_plain, Email::MESSAGE_IS_PLAIN);
                break;

            case 'html+plain':
                $email->message($message_html, $message_plain);
                break;

            case 'html+to-plain':
                $email->message($message_html, Email::MESSAGE_ALTER_PLAIN);
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
    protected function getTransport(string $transport)
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

        return null;
    }
}

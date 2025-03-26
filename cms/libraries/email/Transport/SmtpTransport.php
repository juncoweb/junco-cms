<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Email\Transport;

use Exception;
use EmailException;

class SmtpTransport extends TransportAbstract
{
    // smtp
    public    $smtp_host        = null;
    public    $smtp_port        = null;
    public    $smtp_timeout        = null;
    //
    public    $smtp_secure        = null;
    public    $smtp_auth        = null;
    public    $smtp_user        = null;
    public    $smtp_pwd            = null;
    //
    protected $smtp_resource    = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $config = config('email');

        // smtp
        $this->smtp_host        = $config['email.smtp_host'];
        $this->smtp_port        = $config['email.smtp_port'];
        $this->smtp_timeout        = $config['email.smtp_timeout'];
        $this->smtp_secure        = $config['email.smtp_secure'];
        $this->smtp_auth        = $config['email.smtp_auth'];
        $this->smtp_user        = $config['email.smtp_user'];
        $this->smtp_pwd            = $config['email.smtp_pwd'];
    }

    /**
     * Send
     * 
     * @param Email  $Email
     * 
     * @return bool
     */
    public function send($Email): bool
    {
        // connect
        is_resource($this->smtp_resource) or $this->smtp_connect();

        // from
        $this->smtp_send_command('from', $Email->getBatch('from'));

        // to
        foreach (['to', 'cc', 'bcc'] as $to) {
            foreach ($Email->getBatch($to, true) as $address) {
                $this->smtp_send_command('to', $address);
            }
        }

        // data
        $this->smtp_send_command('data');

        // perform dot transformation on any lines that begin with a dot
        $this->smtp_send_data($Email->getHeader() . preg_replace('/^\./m', '..$1', $Email->getBody()));

        $this->smtp_send_data('.');
        $this->smtp_get_data(250);

        return true;
    }

    // smtp connect
    protected function smtp_connect()
    {
        if (!$this->smtp_host) {
            throw new EmailException('Unknow hostname');
        }
        if (!$this->smtp_port) {
            $this->smtp_port = 25;
        }
        if (!$this->smtp_timeout) {
            $this->smtp_timeout = 30;
        }
        if ($this->smtp_secure == 'tls') {
            $options = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ];
        } else {
            $options = [];
        }

        // connect
        $this->smtp_resource = stream_socket_client(
            $this->smtp_host . ':' . $this->smtp_port,
            $errno,
            $errstr,
            $this->smtp_timeout,
            STREAM_CLIENT_CONNECT,
            stream_context_create($options)
        );

        if (!is_resource($this->smtp_resource)) {
            throw new EmailException($errstr . ' [' . $errno . ']');
        }

        $this->smtp_get_data(220);
        $this->smtp_send_command('hello');

        if ($this->smtp_auth) {
            $this->smtp_authenticate();
        }
    }

    /**
     * Authenticate
     */
    protected function smtp_authenticate()
    {
        if (!$this->smtp_user) {
            throw new EmailException('No user to authenticate');
        }
        if (!$this->smtp_pwd) {
            throw new EmailException('No password to authenticate');
        }
        // tls
        if ($this->smtp_secure == 'tls') {
            $this->smtp_send_data('STARTTLS');
            $this->smtp_get_data(220);

            // http://php.net/manual/en/function.stream-socket-enable-crypto.php#119122
            $crypto_method = STREAM_CRYPTO_METHOD_TLS_CLIENT;

            if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                $crypto_method |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
                $crypto_method |= STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT;
            }

            $crypto = stream_socket_enable_crypto($this->smtp_resource, true, $crypto_method);

            if ($crypto !== true) {
                throw new EmailException('Enable crypto');
            }

            $this->smtp_send_command('hello');
        }

        $this->smtp_send_data('AUTH LOGIN');
        $this->smtp_get_data(334);

        $this->smtp_send_data(base64_encode($this->smtp_user));
        $this->smtp_get_data(334);

        $this->smtp_send_data(base64_encode($this->smtp_pwd));
        $this->smtp_get_data(235);
    }

    /**
     * Send command
     * 
     * @param string $cmd
     * @param string $data
     */
    protected function smtp_send_command(string $cmd, string $data = '')
    {
        switch ($cmd) {
            case 'hello':
                $this->smtp_send_data(($this->smtp_auth ? 'EHLO' : 'HELO') . ' ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
                $this->smtp_get_data(250);
                break;

            case 'from':
                $this->smtp_send_data('MAIL FROM:' . $data);
                $this->smtp_get_data(250);
                break;

            case 'to':
                $this->smtp_send_data('RCPT TO:' . $data);
                $this->smtp_get_data(250);
                break;

            case 'data':
                $this->smtp_send_data('DATA');
                $this->smtp_get_data(354);
                break;

            case 'quit':
                $this->smtp_send_data('QUIT');
                $this->smtp_get_data(221);
                fclose($this->smtp_resource);
                break;
        }
    }

    /**
     * Send data
     * 
     * @param string $data
     */
    protected function smtp_send_data(string $data): void
    {
        if ($this->debug) {
            $this->debug_log[] = '-c: ' . $data;
        }
        if (!@fwrite($this->smtp_resource, $data . PHP_EOL)) {
            throw new EmailException('Failed to send data to smtp server');
        }
    }

    /**
     * Get data
     * 
     * @param int $expect
     * 
     * @return string
     */
    protected function smtp_get_data(int $expect = 0): string
    {
        $data = '';
        while ($line = fgets($this->smtp_resource, 512)) {
            $data .= $line;
            if (substr($line, 3, 1) == ' ') {
                break;
            }
        }
        if ($this->debug) {
            $this->debug_log[] = '-s: ' . $data;
        }
        if ($expect && strncmp($data, (string)$expect, 3) != 0) {
            throw new EmailException(sprintf('Failed to get data to smtp server. Expected «%s» and received «%s»', $expect, \Utils::cutText($data, 20)));
        }

        return $data;
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        is_resource($this->smtp_resource)
            and $this->smtp_send_command('quit');
    }
}

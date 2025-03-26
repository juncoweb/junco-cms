<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 *
 *
 * Settings
 * --------------
 * mime type: text/plain || text/html || multipart/alternative || multipart/mixed || multipart/related
 * enconding: 7bit || 8bit || quoted-printable || base64
 * charset:   iso-8859-1 || utf-8
 */

use Junco\Email\EmailMessageInterface;
use Junco\Email\Transport\TransportInterface;
use Junco\Filesystem\MimeHelper;

class Email
{
    // vars
    protected $transport        = null; // mail || smtp
    protected $no_reply            = null;

    // header
    protected $from                = [];
    protected $return_path        = [];
    protected $reply_to            = [];
    protected $to                = [];
    protected $cc                = [];
    protected $bcc                = [];
    protected $batches            = [];
    protected $custom_header    = [];
    public    $message_id        = '';
    public    $x_mailer            = null;
    public    $priority            = 0;
    protected $subject            = '';
    protected $entities            = null;

    public    $h_charset        = null; // iso-8859-1 || utf-8
    public    $h_enconding        = null; // Q || B

    // message
    public    $m_charset        = null; // iso-8859-1 || utf-8
    public    $m_encoding        = null;  // 8bit || 7bit || quoted-printable
    public    $word_wrap        = 70;
    protected $message            = [];

    // attachment
    protected $attachment        = [];
    public    $attachment_base    = false;

    // more
    protected $CRLF                = "\r\n";
    protected $unique_id        = '';
    protected $counter            = 0;

    // const
    const MESSAGE_IS_PLAIN        = -1;
    const MESSAGE_IS_HTML         = false;
    const MESSAGE_ALTER_PLAIN     = true;

    /**
     * Get message snippet
     */
    public static function getMessage(string $snippet = ''): EmailMessageInterface
    {
        return snippet('email', $snippet);
    }

    /**
     * Constructor
     * 
     * @param TransportInterface $transport
     */
    public function __construct(?TransportInterface $transport = null)
    {
        $config = config('email');
        //
        $this->no_reply            = $config['email.no_reply'];
        // header
        $this->x_mailer            = $config['email.x_mailer'];
        $this->h_charset        = $config['email.charset'];
        $this->h_enconding        = $config['email.header_encoding'];
        // message
        $this->m_charset        = $config['email.charset'];
        $this->m_encoding        = $config['email.message_encoding'];
        $this->transport        = $transport ?? $this->getTransport($config['email.transport']);
    }

    /**
     * Get transport
     * 
     * @param string $transport
     * 
     * @return TransportInterface
     */
    public function getTransport(string $transport): TransportInterface
    {
        switch ($transport) {
            case 'mail':
                return new Junco\Email\Transport\MailTransport;

            case 'smtp':
                return new Junco\Email\Transport\SmtpTransport;

            case 'null':
                return new Junco\Email\Transport\NullTransport;

            default:
                throw new Exception('Unknow transport');
        }
    }

    /**
     * Add a <from> address
     * 
     * @param string|array $address
     */
    public function from(string|array $address): void
    {
        $this->add_address('from', $address, true);
    }

    /**
     * Add a <reply-to> address
     * 
     * @param string|array $address
     */
    public function replyTo(string|array $address): void
    {
        $this->add_address('reply_to', $address, true);
    }

    /**
     * Add a <return-path> address
     * 
     * @param string|array $address
     */
    public function returnPath(string|array $address): void
    {
        $this->add_address('return_path', $address, true);
    }

    /**
     * Add a <to> address
     * 
     * @param string|array $address
     */
    public function to(string|array $address): void
    {
        $this->add_address('to', $address);
    }

    /**
     * Add a <cc> address
     * 
     * @param string|array $address
     */
    public function cc(string|array $address): void
    {
        $this->add_address('cc', $address);
    }

    /**
     * Add a <bcc> address
     * 
     * @param string|array $address
     */
    public function bcc(string|array $address): void
    {
        $this->add_address('bcc', $address);
    }

    /**
     * Add a subject
     * 
     * @param string $subject
     */
    public function subject(string $subject = ''): void
    {
        $this->subject = $subject;
    }

    /**
     * Add a message
     *
     * @param string|object $message
     * @param mixed  $alternative
     *  number  -1    - in case the message is plain
     *  bool    false - in case the message is html and you do not want to send the alternative
     *  bool    true  - in case the message is html and you want to create the alternative automatically
     *  string        - in the case of including an alternative message
     */
    public function message(string|object $message = '', $alternative = false)
    {
        if ($message) {
            $this->message = [];
            if (is_object($message)) {
                $alternative = $message->getPlain();
                $message     = $message->getHtml();
            }
            if ($alternative === self::MESSAGE_IS_PLAIN) {
                $alternative = false;
                $message     = $this->plain_message($message);
                $type        = 'text/plain';
            } else {
                $message     = $this->html_message($message);
                $type        = 'text/html';
            }

            if ($alternative) {
                if ($alternative === true) {
                    $alternative = $message;
                }
                $this->message[] = ['content' => $this->plain_message($alternative), 'type' => 'text/plain'];
            }
            $this->message[] = ['content' => $message, 'type' => $type];
        }
    }

    /**
     * Add a attachment
     * 
     * @param string $filename
     * @param string $name
     * @param string $disposition
     * @param string $cid
     * @param string $encoding
     */
    public function attachment(
        string $filename,
        string $name = '',
        string $disposition = 'attachment',
        string $cid = '',
        string $encoding = 'base64'
    ) {
        if (!$name) {
            $name = basename($filename);
        }
        $filename = $this->attachment_base . $filename;

        // validate
        if (!is_file($filename)) {
            throw new Exception(_t('The attached file was not found.'));
        }
        if (!is_readable($filename)) {
            throw new Exception(_t('The attached file is not readable.'));
        }

        $this->attachment[] = [
            'filename'    => $filename,
            'name'        => $name,
            'encoding'    => $encoding,
            'type'        => (new MimeHelper)->getType($filename),
            'disposition' => $disposition,
            'cid'         => $cid
        ];

        return true;
    }

    /**
     * Add a custom header
     * 
     * @param string $name
     * @param string $value
     */
    public function addCustomHeader(string $name, string $value = '')
    {
        $this->custom_header[$name] = $value;
    }

    /**
     * Clean the queue of addresses to send
     */
    public function clear()
    {
        $this->to = $this->cc = $this->bcc = $this->batches = [];
        $this->unique_id = $this->message_id = '';
        $this->entities = null;
    }

    /**
     * Send
     * 
     * @return bool
     */
    public function send()
    {
        $this->get_mime_batches();
        if (!$this->message) {
            $this->message[] = ['content' => '', 'type' => 'text/plain'];
        }

        // entities
        $entities = $this->get_mime_entities();
        $this->entities = [
            'header' => $this->get_mime_header() . $entities['header'] . $this->CRLF,
            'body' => $entities['body']
        ];

        $result = $this->transport->send($this);
        $this->clear();

        return $result;
    }

    /**
     * Add a subject
     * 
     * @param bool $encode
     */
    public function getSubject(bool $encode = true): string
    {
        return $this->encode_header($this->subject, $encode);
    }

    /**
     * Get batch
     * 
     * @param string $name
     * @param bool   $return_array
     */
    public function getBatch(string $name, bool $return_array = false): string|array
    {
        if ($return_array) {
            return $this->$name;
        }

        return implode(',', $this->$name);
    }

    /**
     * Get header
     */
    public function getHeader(): string
    {
        return $this->entities['header'];
    }

    /**
     * Get body
     */
    public function getBody(): string
    {
        return $this->entities['body'];
    }

    /**
     * Get
     */
    protected function get_mime_batches(): void
    {
        if (!$this->to && !$this->cc && !$this->bcc) {
            throw new Exception(_t('Please, complete at least one target mail.'));
        }
        if (!$this->from) {
            $this->from([
                'name' => config('site.name'),
                'address' => $this->no_reply
            ]);
        }

        foreach (['from', 'return_path', 'reply_to', 'to', 'cc', 'bcc'] as $batch) {
            $this->batches[$batch] = implode(',', $this->$batch);
        }
    }

    /**
     * Get mime header
     */
    protected function get_mime_header(): string
    {
        if (!$this->message_id) {
            $this->message_id = '<' . $this->getUniqueID() . '@' . ($_SERVER['SERVER_NAME'] ?? 'localhost') . '>';
        }

        $header = [
            'MIME-Version'    => '1.0',
            'Date'            => date(DATE_RFC2822),
            'Message-ID'    => $this->message_id,
            'From'            => $this->batches['from'],
            'ReplyTo'        => $this->batches['reply_to'] ?: $this->batches['from']
        ];

        if ($this->return_path) {
            $header['Return-Path']    = $this->batches['return_path'];
        }
        if ($this->cc) {
            $header['CC']            = $this->batches['cc'];
        }
        if ($this->transport instanceof Junco\Email\Transport\MailTransport) {
            if ($this->bcc) {
                $header['BCC']        = $this->batches['bcc'];
            }
        } else {
            if ($this->to) {
                $header['To']        = $this->batches['to'];
            }
            if ($this->subject) {
                $header['Subject']    = $this->encode_header($this->subject, true, 9);
            }
        }

        // Others
        if ($this->x_mailer) {
            $header['X-Mailer']        = $this->x_mailer;
        }
        if ($this->priority) {
            $header['X-Priority']    = $this->priority;
        }
        if ($this->custom_header) {
            $header += $this->custom_header;
        }

        return $this->to_string_mime_header($header);
    }

    /**
     * Get multipart entity
     * 
     * @param string $type
     * @param array  $entities
     */
    protected function get_multipart_entity(string $type, array $entities)
    {
        // vars
        $boundary = 'boundary_' . ($this->counter++) . '_' . $this->getUniqueID();
        $body     = '';

        foreach ($entities as $entity) {
            $body .= '--' . $boundary . $this->CRLF
                . $entity['header'] . $this->CRLF
                . $entity['body'] . $this->CRLF . $this->CRLF;
        }

        $body .= '--' . $boundary . '--';

        return [
            'header' => 'Content-Type: multipart/' . $type . ';' . $this->CRLF . ' boundary="' . $boundary . '"' . $this->CRLF,
            'body' => $body
        ];
    }

    /**
     * Get mime entities
     */
    protected function get_mime_entities()
    {
        $entities   = [];
        $attached   = [];
        $is_related = false;

        // message
        foreach ($this->message as $row) {
            $entities[] = [
                'header' => $this->to_string_mime_header([
                    'Content-Type'                => $row['type'] . '; charset=' . $this->m_charset,
                    'Content-Transfer-Encoding'    => $this->m_encoding
                ]),
                'body'    => $this->encode_string($row['content'], $this->m_encoding)
            ];
        }

        if (count($entities) > 1) {
            $partial  = $this->get_multipart_entity('alternative', $entities);
            $entities = [$partial];
        }

        // attachment
        foreach ($this->attachment as $row) {
            if (!in_array($row['filename'], $attached)) {
                $attached[] = $row['filename'];
                $header = [
                    'Content-Type'                => $row['type'] . '; name="' . $row['name'] . '"',
                    'Content-Disposition'        => $row['disposition'] . '; filename="' . $row['name'] . '"',
                    'Content-Transfer-Encoding' => $row['encoding'],
                ];

                if ($row['disposition'] == 'inline') {
                    $header['Content-ID']    = '<' . $row['cid'] . '>';
                    $is_related                = true;
                }

                $buffer        = @file_get_contents($row['filename']);
                $entities[] = [
                    'header' => $this->to_string_mime_header($header),
                    'body'     => $this->encode_string($buffer, $row['encoding'])
                ];
            }
        }

        if (count($entities) > 1) {
            return $this->get_multipart_entity($is_related ? 'related' : 'mixed', $entities);
        }

        return $entities[0];
    }

    /**
     * To string mime header
     * 
     * @param array $header
     * 
     * @return string
     */
    protected function to_string_mime_header(array $header): string
    {
        $h = '';
        foreach ($header as $name => $value) {
            $h .= $name . ': ' . $value . $this->CRLF;
        }

        return $h;
    }

    /**
     * Add address
     *
     * @param string       $batch    Possible values: from, to, reply_to, cc, bcc
     * @param string|array $address  The email address. Formats: 
     *    1. "example@example.com"
     *    2. "<example@example.com>"
     *    3. "Example <example@example.com>"
     *    4. ['name' => 'Example', 'address' => 'example@example.com']
     * @param bool $only_one
     * 
     * @throws Exception
     */
    protected function add_address(string $batch, string|array $address, bool $only_one = false): void
    {
        if (is_string($address)) {
            $addresses = explode(',', $address);

            foreach ($addresses as $address) {
                $address = explode('<', $address, 2);

                if (count($address) == 1) {
                    $this->push_address($batch, $address[0]);
                } else {
                    $this->push_address($batch, $address[1], $address[0]);
                }
            }
        } else {
            $this->push_address($batch, $address['address'] ?? '', $address['name'] ?? '');
        }

        if ($only_one && count($this->$batch) > 1) {
            throw new Exception(_t('There are many email addresses where only one is accepted.'));
        }
    }

    /**
     * Push address
     *
     * @param string $name
     * @param string $address
     */
    protected function push_address(string $batch, string $address, string $name = '')
    {
        $address = trim($address, "\s>");

        if (!$address) {
            throw new Exception(_t('The email address not found.'));
        }
        if ($name) {
            $name = $this->encode_header($name);
            $address = "$name <$address>";
        } else {
            $address = "<$address>";
        }

        $this->$batch[] = $address;
    }

    /**
     * Html message
     * 
     * @param string $message
     * 
     * @return string
     */
    protected function html_message(string $message): string
    {
        preg_match_all("/(src|background)\s*=\s*\"([^\"]*)\"/ui", $message, $matches);

        foreach ($matches[2] as $i => $filename) {
            // do not change urls for absolute images / *phpmailer
            if (!preg_match('#^[A-z]+://#iu', $filename)) {
                $cid = md5($filename);

                if ($this->attachment($filename, false, 'inline', $cid)) {
                    $message = preg_replace('/' . $matches[1][$i] . '\s*=\s*"' . preg_quote($filename, '/') . '"/i', $matches[1][$i] . '="cid:' . $cid . '"', $message);
                }
            }
        }

        return $message;
    }

    /**
     * Filter the message, making sure to leave plain text
     * 
     * @param string $messge
     * 
     * @return string
     */
    protected function plain_message(string $message): string
    {
        return wordwrap(trim(strip_tags(preg_replace('/<(head|title|style|script)[^>]*>.*?<\/\\1>/s', '', $message))), $this->word_wrap);
    }

    /**
     * Encode + secure header
     * 
     * @param string $string
     * @param bool   $encode
     * @param int    $indent
     * 
     * @return string
     */
    protected function encode_header(string $string, bool $encode = true, int $indent = 0): string
    {
        // secure
        $string = str_replace(["\n", "\r"], '', trim($string));

        return $encode
            ? mb_encode_mimeheader($string, $this->h_charset, $this->h_enconding, $this->CRLF, $indent)
            : $string;
    }

    /**
     * Encode
     * 
     * @param string $string
     * @param string $encoding
     * 
     * @return string
     */
    protected function encode_string(string $string, string $encoding): string
    {
        switch ($encoding) {
            case 'base64':
                return rtrim(chunk_split(base64_encode($string), 76, $this->CRLF));
            case '7bit':
            case '8bit':
                return preg_replace("/(?<=[^\r]|^)\n/", "\r\n", $string); // fix LE ??? to improve
            case 'quoted-printable':
                return quoted_printable_encode($string);
        }

        return $string;
    }

    /**
     * @see https://gist.github.com/Robbert/4967898
     * @see http://www.jwz.org/doc/mid.html
     */
    protected function getUniqueID()
    {
        if (!$this->unique_id) {
            $this->unique_id = base_convert(time(), 10, 36)
                . '.' . base_convert(bin2hex(random_bytes(8)), 16, 36);
        }

        return $this->unique_id;
    }
}

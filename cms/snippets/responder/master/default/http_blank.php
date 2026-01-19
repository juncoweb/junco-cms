<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Filesystem\MimeHelper;
use Junco\Http\Message\HttpFactory;
use Junco\Mvc\Result;
use Junco\Responder\ResponderBase;
use Junco\Responder\Contract\HttpBlankInterface;
use Psr\Http\Message\ResponseInterface;

class responder_master_default_http_blank extends ResponderBase implements HttpBlankInterface
{
    // vars
    protected string $content       = '';
    protected string $filename      = '';
    protected string $ContentType   = '';
    protected int    $ContentLength = 0;

    /**
     * Create File
     * 
     * @param string $content
     * @param string $filename
     * @param string $ContentType
     * @param string $extension
     */
    public function createFile(
        string $content,
        string $filename = '',
        string $ContentType = '',
        string $extension = ''
    ): void {
        if (
            $extension
            && $filename
            && $extension != pathinfo($filename, PATHINFO_EXTENSION)
        ) {
            $filename .= '.' . $extension;
        }
        if (!$ContentType) {
            $ContentType = (new MimeHelper)->getType($filename);
        }

        $this->content       = $content;
        $this->filename      = $filename;
        $this->ContentType   = $ContentType;
        $this->ContentLength = strlen($content);
    }

    /**
     * Create Json File
     * 
     * @param array  $json
     * @param string $filename
     */
    public function createJsonFile(array $json, string $filename = ''): void
    {
        $this->createFile(json_encode($json), $filename, 'application/json', 'json');
    }

    /**
     * Create CSV File
     * 
     * @param array  $rows
     * @param string $filename
     */
    public function createCSVFile(array $rows, string $filename = ''): void
    {
        $csv = "\xEF\xBB\xBF"; // BOM

        foreach ($rows as $row) {
            foreach ($row as $k => $value) {
                if ($value && (false !== strpos($value, ';') || false !== strpos($value, '"'))) {
                    $row[$k] = '"' . str_replace('"', '""', $row[$k]) . '"';
                }
            }

            $csv .= implode(';', $row) . PHP_EOL;
        }

        $this->createFile($csv, $filename, 'text/csv', 'csv');
    }

    /**
     * Create SQL File
     * 
     * @param string $sql
     * @param string $filename
     */
    public function createSqlFile(string $sql, string $filename = ''): void
    {
        $this->createFile($sql, $filename, 'application/sql', 'sql');
    }

    /**
     * Adds a file from its path.
     * 
     * @param string $file
     * @param string $filename
     * 
     * @return bool
     */
    public function appendFile(string $file, string $filename = ''): bool
    {
        if (!is_file($file)) {
            return false;
        }
        $this->content       = (new HttpFactory)->createStreamFromFile($file);
        $this->filename      = $filename ?: pathinfo($file, PATHINFO_BASENAME);
        $this->ContentType   = (new MimeHelper)->getType($file);
        $this->ContentLength = filesize($file);

        return true;
    }

    /**
     * Creates a simplified response with a message.
     * 
     * @param Result|string $message
     * @param int $statusCode
     * @param int $code
     * 
     * @return ResponseInterface
     */
    public function responseWithMessage(Result|string $message = '', int $statusCode = 0, int $code = 0): ResponseInterface
    {
        if ($message instanceof Result) {
            $statusCode = $message->getStatusCode();
            $code       = $message->getCode();
            $message    = $message->getMessage();
        }

        $factory = new HttpFactory;
        $stream = $factory->createStream(sprintf('%d - %s', $code, $message));

        return $factory
            ->createResponse($statusCode)
            ->withBody($stream);
    }

    /**
     * Create a response.
     * 
     * @param int $statusCode
     * @param string $reasonPhrase
     * 
     * @return ResponseInterface
     */
    public function response(int $statusCode = 200, string $reasonPhrase = ''): ResponseInterface
    {
        $factory = new HttpFactory;
        $stream = $this->content;

        if (is_string($stream)) {
            $stream = $factory->createStream($stream);
        }

        // response
        $response = $factory
            ->createResponse($statusCode, $reasonPhrase)
            ->withBody($stream);

        $headers['Pragma'] = 'public';
        $headers['Expires'] = '0';
        $headers['Cache-Control'] = ['post-check=0', 'pre-check=0', 'private'];
        $headers['Content-Type'] = $this->ContentType;
        $headers['Content-Disposition'] = "attachment; filename=\"$this->filename\"";
        $headers['Content-Length'] = $this->ContentLength;

        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }
        return $response;
    }
}

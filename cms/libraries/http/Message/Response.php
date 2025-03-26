<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Message;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Junco\Http\Message\Message;

/**
 * Representation of an outgoing, server-side response.
 */
class Response extends Message implements ResponseInterface
{
    // vars
    protected int    $statusCode = 200;
    protected string $reasonPhrase;

    // const
    protected const PHRASES = [
        100 => "Continue",
        101 => "Switching Protocols",
        102 => "Processing",
        103 => "Early Hints",
        //
        200 => "OK",
        201 => "Created",
        202 => "Accepted",
        203 => "Non-Authoritative Information",
        204 => "No Content",
        205 => "Reset Content",
        206 => "Partial Content",
        207 => "Multi-Status",
        208 => "Already Reported",
        226 => "IM Used",
        //
        300 => "Multiple Choices",
        301 => "Moved Permanently",
        302 => "Found",
        303 => "See Other",
        304 => "Not Modified",
        305 => "Use Proxy",
        306 => "(Unused)",
        307 => "Temporary Redirect",
        308 => "Permanent Redirect",
        //
        400 => "Bad Request",
        401 => "Unauthorized",
        402 => "Payment Required",
        403 => "Forbidden",
        404 => "Not Found",
        405 => "Method Not Allowed",
        406 => "Not Acceptable",
        407 => "Proxy Authentication Required",
        408 => "Request Timeout",
        409 => "Conflict",
        410 => "Gone",
        411 => "Length Required",
        412 => "Precondition Failed",
        413 => "Content Too Large",
        414 => "URI Too Long",
        415 => "Unsupported Media Type",
        416 => "Range Not Satisfiable",
        417 => "Expectation Failed",
        418 => "(Unused)",
        421 => "Misdirected Request",
        422 => "Unprocessable Content",
        423 => "Locked",
        424 => "Failed Dependency",
        425 => "Too Early",
        426 => "Upgrade Required",
        427 => "Unassigned",
        428 => "Precondition Required",
        429 => "Too Many Requests",
        430 => "Unassigned",
        431 => "Request Header Fields Too Large",
        451 => "Unavailable For Legal Reasons",
        //
        500 => "Internal Server Error",
        501 => "Not Implemented",
        502 => "Bad Gateway",
        503 => "Service Unavailable",
        504 => "Gateway Timeout",
        505 => "HTTP Version Not Supported",
        506 => "Variant Also Negotiates",
        507 => "Insufficient Storage",
        508 => "Loop Detected",
        509 => "Unassigned",
        510 => "Not Extended (OBSOLETED)",
        511 => "Network Authentication Required",
    ];

    /**
     * Constructor
     * 
     * @param int                                  $status  Status code
     * @param (string|string[])[]                  $headers Response headers
     * @param string|resource|StreamInterface|null $body    Response body
     * @param string                               $version Protocol version
     * @param string|null                          $reason  Reason phrase (when empty a default will be used based on the status code)
     */
    public function __construct(
        ?int                $status = null,
        array                $headers = [],
        ?StreamInterface    $stream = null,
        ?string                $version = null,
        string                $reasonPhrase = ''
    ) {
        if ($status) {
            $this->statusCode = $this->filterStatusCode($status);
        }
        $this->reasonPhrase = $reasonPhrase ?: (self::PHRASES[$this->statusCode] ?? '');

        if ($headers) {
            $this->setHeaders($headers);
        }
        if ($version !== null) {
            $this->verifyProtocolVersion($version);
            $this->version = $version;
        }

        parent::__construct($stream ?? new Stream('php://memory', 'wb+'));
    }

    /**
     * Gets the response status code.
     *
     * @return int Status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     * 
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the provided status code.
     * 
     * @return static
     * 
     * @throws \InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        $new = clone $this;
        $new->statusCode = $this->filterStatusCode($code);
        $new->reasonPhrase = $reasonPhrase ?: (self::PHRASES[$new->statusCode] ?? '');

        return $new;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     * 
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    /**
     * Filter the status code.
     * 
     * @param int $statusCode
     * 
     * @return int $statusCode
     */
    protected function filterStatusCode(int $statusCode): int
    {
        if ($statusCode < 100 || $statusCode >= 600) {
            throw new \InvalidArgumentException('Status code must be an integer value between 1xx and 5xx.');
        }

        return $statusCode;
    }
}

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Client\Builder;

use Psr\Http\Message\ResponseInterface;

/**
 * Processes raw data and builds a Response.
 */
class ResponseBuilder
{
    /**
     * The response to be built.
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Constructor
     * 
     * @param ResponseInterface $response
     */
    public function __construct(?ResponseInterface $response = null)
    {
        $this->response = $response;
    }

    /**
     * Get response.
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set response status from a status string.
     *
     * @param string $statusLine
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setStatus($statusLine)
    {
        if (!preg_match('/^(?:HTTP\/(\d\.\d)\s(\d{3})(?:\s(.*)?)?)$/', $statusLine, $match)) {
            throw new \InvalidArgumentException(sprintf('Invalid status line: "%s"', $statusLine));
        }

        $this->response = $this->response
            ->withStatus($match[2], $match[3] ?? '')
            ->withProtocolVersion($match[1]);

        return $this;
    }

    /**
     * Add headers represented by a single string.
     *
     * @param string $headers
     *
     * @return $this
     */
    public function setHeadersFromString(string $headers)
    {
        return $this->setHeadersFromArray(explode("\r\n", $headers));
    }

    /**
     * Set headets
     *
     * @param string[] $headers
     *
     * @return $this
     */
    public function setHeadersFromArray(array $headers)
    {
        $this->setStatus(array_shift($headers));

        foreach ($headers as $headerLine) {
            $this->setHeader($headerLine);
        }

        return $this;
    }

    /**
     * Set headets
     *
     * @param string $headerLine
     *
     * @return $this
     */
    public function setHeader(string $headerLine)
    {
        if (strpos($headerLine, ':') !== false) {
            $part = explode(':', $headerLine, 2);

            $this->response = $this->response->withAddedHeader(
                trim($part[0]),
                trim($part[1])
            );
        }

        return $this;
    }

    /**
     * Write the content of the body.
     *
     * @param string $content
     *
     * @return $this
     */
    public function setBody(string $content)
    {
        $this->response->getBody()->write($content);

        return $this;
    }
}

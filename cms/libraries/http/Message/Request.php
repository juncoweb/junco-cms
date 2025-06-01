<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Message;

use Junco\Http\Message\Message;
use Junco\Http\Message\Stream;
use Junco\Http\Message\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Representation of an outgoing, client-side request.
 */
class Request extends Message implements RequestInterface
{
    // vars
    protected string $method         = 'GET';
    protected ?string $requestTarget = null;
    protected ?UriInterface $uri;

    /**
     * Constructor
     * 
     * @param string				$method   HTTP method for the request, if any.
     * @param string|UriInterface   $uri      URI for the request, if any.
     * @param array					$headers  Headers for the message, if any.
     * @param ?StreamInterface		$stream   Message body, if any.
     * @param ?string				$version  Protocol version
     * 
     * @throws \InvalidArgumentException For any invalid value.
     */
    public function __construct(
        string              $method,
        string|UriInterface $uri,
        ?array              $headers = null,
        ?StreamInterface    $stream = null,
        ?string             $version = null
    ) {
        if (is_string($uri)) {
            $uri = new Uri($uri);
        }

        $this->method = $this->filterMethod($method);
        $this->uri    = $uri;

        if ($headers) {
            $this->setHeaders($headers);
        }

        /** 
         * [PSR-7] During construction, implementations MUST attempt to set the Host header
         * from a provided URI if no Host header is provided.
         */
        if (!isset($this->headerNames['host'])) {
            $this->updateHostFromUri();
        }

        if ($version !== null) {
            $this->verifyProtocolVersion($version);
            $this->version = $version;
        }

        parent::__construct($stream ?? new Stream('php://memory', 'wb+'));
    }

    /**
     * Retrieves the message's request target.
     *
     * @return string
     */
    public function getRequestTarget(): string
    {
        if (null !== $this->requestTarget) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();
        if ($this->uri->getQuery()) {
            $target .= '?' . $this->uri->getQuery();
        }

        return $target ?: '/';
    }

    /**
     * Return an instance with the specific request-target.
     * 
     * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     * 
     * @param string $requestTarget
     * 
     * @return static
     */
    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new \InvalidArgumentException('The request target is not valid');
        }

        $new = clone $this;
        $new->requestTarget = $requestTarget;

        return $new;
    }


    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * @param string $method Case-sensitive method.
     * 
     * @return static
     * 
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod(string $method): RequestInterface
    {
        $new = clone $this;
        $new->method = $this->filterMethod($method);

        return $new;
    }

    /**
     * Retrieves the URI instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * 
     * @return UriInterface
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * Returns an instance with the provided URI.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * 
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * 
     * @return static
     */
    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        if ($uri === $this->uri) {
            return $this;
        }

        $new = clone $this;
        $new->uri = $uri;

        if (!$preserveHost || !isset($this->headerNames['host'])) {
            $new->updateHostFromUri();
        }

        return $new;
    }

    /**
     * Filter method
     *
     * @param string $method Case-sensitive method.
     * 
     * @return string The method
     * 
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    protected function filterMethod(string $method): string
    {
        $method = strtoupper($method);

        if (!in_array($method, ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'])) {
            throw new \InvalidArgumentException(sprintf('Unsupported HTTP method «%s» provided', $method ?: 'Unknown'));
        }

        return $method;
    }

    /**
     * 
     */
    protected function updateHostFromUri(): void
    {
        $host = $this->uri->getHost();

        if ($host == '') {
            return;
        }

        $port = $this->uri->getPort();
        if ($port !== null) {
            $host .= ':' . $port;
        }

        if (isset($this->headerNames['host'])) {
            $name = $this->headerNames['host'];
            unset($this->headers[$name]);
        } else {
            $name = 'Host';
            $this->headerNames['host'] = $name;
        }

        /** @see: https://datatracker.ietf.org/doc/html/rfc7230#section-5.4 */
        $this->headers = array_merge([$name => [$host]], $this->headers);
    }
}

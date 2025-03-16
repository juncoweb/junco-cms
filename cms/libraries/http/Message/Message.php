<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Message;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * HTTP messages for requests from a client to a server and their responses.
 */
class Message implements MessageInterface
{
	// vars
	protected string $version		= '1.1';
	protected array  $headers		= [];
	protected array  $headerNames	= [];
	protected StreamInterface $stream;

	/**
	 * Constructor
	 * 
	 * @param StreamInterface $stream
	 */
	public function __construct(StreamInterface $stream)
	{
		$this->stream = $stream;
	}

	/**
	 * Retrieves the HTTP protocol version as a string.
	 *
	 * @return string HTTP protocol version.
	 */
	public function getProtocolVersion(): string
	{
		return $this->version;
	}

	/**
	 * Return an instance with the specified HTTP protocol version.
	 *
	 * @param string $version HTTP protocol version
	 * 
	 * @return static
	 */
	public function withProtocolVersion(string $version): MessageInterface
	{
		$this->verifyProtocolVersion($version);

		$new = clone $this;
		$new->version = $version;

		return $new;
	}

	/**
	 * Retrieves all message header values.
	 *
	 * @return string[][]
	 */
	public function getHeaders(): array
	{
		return $this->headers;
	}

	/**
	 * Checks if a header exists by the given case-insensitive name.
	 *
	 * @param string $name Case-insensitive header field name.
	 * 
	 * @return bool
	 */
	public function hasHeader(string $name): bool
	{
		return isset($this->headerNames[strtolower($name)]);
	}

	/**
	 * Retrieves a message header value by the given case-insensitive name.
	 * 
	 * @param string $name Case-insensitive header field name.
	 * 
	 * @return string[]
	 */
	public function getHeader(string $name): array
	{
		$name = $this->headerNames[strtolower($name)] ?? null;

		if ($name === null) {
			return [];
		}
		return $this->headers[$name];
	}

	/**
	 * Retrieves a comma-separated string of the values for a single header.
	 *
	 * This method returns all of the header values of the given
	 * case-insensitive header name as a string concatenated together using
	 * a comma.
	 *
	 * NOTE: Not all header values may be appropriately represented using
	 * comma concatenation. For such headers, use getHeader() instead
	 * and supply your own delimiter when concatenating.
	 *
	 * If the header does not appear in the message, this method MUST return
	 * an empty string.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @return string A string of values as provided for the given header
	 *    concatenated together using a comma. If the header does not appear in
	 *    the message, this method MUST return an empty string.
	 */
	public function getHeaderLine(string $name): string
	{
		$header = $this->getHeader($name);

		if (!$header) {
			return '';
		}
		return implode(',', $header);
	}

	/**
	 * Return an instance with the provided value replacing the specified header.
	 *
	 * @param string          $name  Case-insensitive header field name.
	 * @param string|string[] $value Header value(s).
	 * 
	 * @return static
	 * 
	 * @throws \InvalidArgumentException for invalid header names or values.
	 */
	public function withHeader(string $name, $value): MessageInterface
	{
		$this->verifyHeaderName($name);

		$new = $this->withoutHeader($name);
		$new->headers[$name] = $this->filterHeaderValue($value, $name);
		$new->headerNames[strtolower($name)] = $name;

		return $new;
	}

	/**
	 * Return an instance with the specified header appended with the given value.
	 * 
	 * @param string          $name Case-insensitive header field name to add.
	 * @param string|string[] $value Header value(s).
	 * 
	 * @return static
	 * 
	 * @throws \InvalidArgumentException for invalid header names or values.
	 */
	public function withAddedHeader(string $name, $value): MessageInterface
	{
		$this->verifyHeaderName($name);

		$new		= clone $this;
		$value		= $new->filterHeaderValue($value, $name);
		$normalized = strtolower($name);

		if ($new->hasHeader($normalized)) {
			$original					= $new->headerNames[$normalized];
			$new->headers[$original]	= array_merge($this->headers[$original], $value);
		} else {
			$new->headers[$name]           = $value;
			$new->headerNames[$normalized] = $name;
		}

		return $new;
	}

	/**
	 * Return an instance without the specified header.
	 *
	 * @param string $name Case-insensitive header field name to remove.
	 * 
	 * @return static
	 */
	public function withoutHeader(string $name): MessageInterface
	{
		if (!$this->hasHeader($name)) {
			return clone $this;
		}

		$new = clone $this;
		$normalized = strtolower($name);

		unset(
			$new->headers[$this->headerNames[$normalized]],
			$new->headerNames[$normalized]
		);

		return $new;
	}

	/**
	 * Gets the body of the message.
	 *
	 * @return StreamInterface Returns the body as a stream.
	 */
	public function getBody(): StreamInterface
	{
		return $this->stream;
	}

	/**
	 * Return an instance with the specified message body.
	 *
	 * @param StreamInterface $body Body.
	 * 
	 * @return static
	 * 
	 * @throws \InvalidArgumentException When the body is not valid.
	 */
	public function withBody(StreamInterface $body): MessageInterface
	{
		$new = clone $this;
		$new->stream = $body;

		return $new;
	}

	/**
	 * Verify HTTP protocol version.
	 *
	 * @param string $version HTTP protocol version
	 * 
	 * @throws bool \InvalidArgumentException
	 */
	public function verifyProtocolVersion(string $version): void
	{
		if (!in_array($version, ['1.0', '1.1', '2.0', '3.0'], true)) {
			throw new \InvalidArgumentException(sprintf('Unsupported HTTP protocol version «%s» provided', $version));
		}
	}

	/**
	 * @param (string|string[])[] $headers
	 */
	protected function setHeaders(array $headers): void
	{
		$this->headerNames = $this->headers = [];

		foreach ($headers as $name => $value) {
			$this->verifyHeaderName($name);
			$normalized = strtolower($name);

			if (isset($this->headerNames[$normalized])) {
				throw new \InvalidArgumentException(sprintf('Cannot add duplicate header «%s»', $name));
			}

			$this->headerNames[$normalized]	= $name;
			$this->headers[$name]			= $this->filterHeaderValue($value, $name);
		}
	}

	/**
	 * Verify Header Name
	 * 
	 * @see http://tools.ietf.org/html/rfc7230#section-3.2
	 * 
	 * @param string
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function verifyHeaderName(string $name): void
	{
		if (!$name || !preg_match('/^[a-zA-Z0-9!#$%&\'*+-.^_`|~]+$/D', $name)) {
			throw new \InvalidArgumentException(sprintf('The header name «%s» is not valid', $name));
		}
	}

	/**
	 * Filter header value
	 * 
	 * @param mixed $values
	 * 
	 * @return array
	 */
	protected function filterHeaderValue(string|array $values, string $name): array
	{
		if (is_array($values)) {
			if (!$values) {
				throw new \InvalidArgumentException(sprintf('The value of the header «%s» is empty', $name));
			}
			$values = array_values($values);
		} else {
			$values = [$values];
		}

		foreach ($values as $i => $value) {
			// Remove optional whitespace (OWS, RFC 7230#3.2.3) around the header value.
			$value = trim((string)$value, "\t ");

			if (!$this->verifyHeaderValue($value)) {
				throw new \InvalidArgumentException(sprintf('The value of the header «%s» is invalid; The current value is «%s»', $name, $value));
			}

			// Normalize line folding to a single space (RFC 7230#3.2.4).
			$value = str_replace(["\r\n\t", "\r\n "], ' ', $value);

			$values[$i] = $value;
		}

		return $values;
	}

	/**
	 * Validate a header value.
	 *
	 * @param mixed $value
	 * 
	 * @return bool
	 */
	protected function verifyHeaderValue(string $value): bool
	{
		if (!$value && $value !== '0') {
			return false;
		}
		// Look for:
		// \n not preceded by \r, OR
		// \r not followed by \n, OR
		// \r\n not followed by space or horizontal tab; these are all CRLF attacks
		if (preg_match("#(?:(?:(?<!\r)\n)|(?:\r(?!\n))|(?:\r\n(?![ \t])))#", $value)) {
			return false;
		}
		// Non-visible, non-whitespace characters
		// 9 === horizontal tab
		// 10 === line feed
		// 13 === carriage return
		// 32-126, 128-254 === visible
		// 127 === DEL (disallowed)
		// 255 === null byte (disallowed)
		if (preg_match('/[^\x09\x0a\x0d\x20-\x7E\x80-\xFE]/', $value)) {
			return false;
		}

		return true;
	}
}

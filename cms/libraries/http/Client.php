<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Junco\Http\Client\Adapter\AdapterInterface;
use Junco\Http\Client\ClientResponseInterface;
use Junco\Http\Client\Factory;

class Client implements ClientInterface
{
	// vars
	protected AdapterInterface $adapter;
	protected $factory = null;

	/**
	 * Constructor
	 *
	 * @param array $config
	 */
	public function __construct(array $config = [])
	{
		$this->adapter = $this->getAdapter($config['adapter'] ?? '');
		$this->factory = new Factory;
	}

	/**
	 * Sends a PSR-7 request and returns a PSR-7 response.
	 *
	 * @param RequestInterface $request
	 *
	 * @return ClientResponseInterface
	 *
	 * @throws \Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
	 */
	public function sendRequest(RequestInterface $request): ClientResponseInterface
	{
		return $this->adapter->sendRequest($request);
	}

	/**
	 * Get adapter
	 *
	 * @param string $adapter
	 *
	 * @return AdapterInterface
	 */
	public function getAdapter(string $adapter): AdapterInterface
	{
		if (!$adapter) {
			$adapter = function_exists('curl_init')
				? 'curl'
				: 'url';
		}

		switch ($adapter) {
			case 'curl':
				return new Client\Adapter\CurlAdapter;
			default:
			case 'url':
				return new Client\Adapter\UrlAdapter;
		}
	}

	/**
	 * Create and send an HTTP request.
	 *
	 * @param string $method
	 * @param string $uri
	 * @param array  $options
	 *
	 * @return ClientResponseInterface
	 */
	public function request(string $method, string $uri = '', array $options = []): ClientResponseInterface
	{
		$data = $options['data'] ?? null;

		// uri
		$uri = $this->factory->createUri($uri);

		if (in_array($method, ['GET', 'HEAD', 'TRACE'])) {
			if (isset($options['query'])) { // Wink for Guzzlehttp ;)
				$data = $options['query'];
			}
			if ($data) {
				$uri = $uri->withQuery(is_array($data) ? http_build_query($data) : $data);
				$data = null;
			}

			$options['multipart'] = null;
		}

		// request
		$request = $this->factory->createRequest($method, $uri);

		// protocol version
		if (!empty($options['version'])) {
			$request = $request->withProtocolVersion($options['version']);
		}
		// headers
		if (!empty($options['headers'])) {
			foreach ($options['headers'] as $name => $value) {
				$request = $request->withHeader($name, $value);
			}
		}

		// body
		if (isset($options['body'])) {
			$request = $request->withBody(
				$this->createStreamFromBody($options['body'])
			);
		} elseif (isset($options['multipart'])) {
			/* if ($request->hasHeader('Content-Type')) {
				throw new \InvalidArgumentException('The query does not accept the "Content-type" header');
			} */
			if (!is_array($options['multipart'])) {
				throw new \InvalidArgumentException('The "multipart" parameter must be an array');
			}
			if ($data) {
				foreach ($data as $name => $value) {
					$options['multipart'][] = [
						'name' => $name,
						'contents' => $value
					];
				}
			}

			$stream   = $this->factory->createStreamFromMultipart($options['multipart']);
			$boundary = $this->factory->getBoundary();
			$request  = $request->withHeader('Content-Type', "multipart/form-data; boundary=$boundary");
			$request  = $request->withBody($stream);
		} elseif ($data) {
			$request = $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');
			$request = $request->withBody(
				$this->getStreamFromData($data)
			);
		} elseif (isset($options['json'])) {
			$request = $request->withHeader('Content-Type', 'application/json');
			$request = $request->withBody(
				$this->getStreamFromJson($options['json'])
			);
		}

		return $this->adapter->sendRequest($request);
	}

	/**
	 * Get the stream for the request body.
	 * 
	 * @param mixed $stream
	 *
	 * @return StreamInterface
	 */
	public function createStreamFromBody($stream): StreamInterface
	{
		if ($stream instanceof StreamInterface) {
			return $stream;
		}
		if (is_string($stream)) {
			return $this->factory->createStream($stream);
		}
		if (is_resource($stream)) {
			return $this->factory->createStreamFromResource($stream);
		}

		throw new \InvalidArgumentException(sprintf(
			'The body of request must be string, resource or StreamInterface; Currently it is: %s',
			gettype($stream)
		));
	}

	/**
	 * Get the stream for the request body.
	 * 
	 * @param mixed $data
	 *
	 * @return StreamInterface
	 */
	public function getStreamFromData($data): StreamInterface
	{
		if (is_array($data)) {
			return $this->factory->createStream(http_build_query($data));
		}
		if (is_string($data)) {
			return $this->factory->createStream($data);
		}

		throw new \InvalidArgumentException(sprintf(
			'The data of request must be string or array; Currently it is: %s',
			gettype($data)
		));
	}

	/**
	 * Get the stream for the request body.
	 * 
	 * @param mixed $json
	 *
	 * @return StreamInterface
	 */
	public function getStreamFromJson($json): StreamInterface
	{
		if (is_array($json)) {
			return $this->factory->createStream(json_encode($json));
		}
		if (is_string($json)) {
			return $this->factory->createStream($json);
		}

		throw new \InvalidArgumentException(sprintf(
			'The json of request must be string or array; Currently it is: %s',
			gettype($json)
		));
	}

	/**
	 * Request with "GET" method.
	 * 
	 * @param string $uri
	 * @param array  $options
	 *
	 * @return ClientResponseInterface
	 */
	public function get(string $uri = '', array $options = []): ClientResponseInterface
	{
		return $this->request('GET', $uri, $options);
	}

	/**
	 * Request with "HEAD" method.
	 * 
	 * @param string $uri
	 * @param array  $options
	 *
	 * @return ClientResponseInterface
	 */
	public function head(string $uri = '', array $options = []): ClientResponseInterface
	{
		return $this->request('HEAD', $uri, $options);
	}

	/**
	 * Request with "POST" method.
	 * 
	 * @param string $uri
	 * @param array  $options
	 *
	 * @return ClientResponseInterface
	 */
	public function post(string $uri = '', array $options = []): ClientResponseInterface
	{
		return $this->request('POST', $uri, $options);
	}

	/**
	 * Request with "PUT" method.
	 * 
	 * @param string $uri
	 * @param array  $options
	 *
	 * @return ClientResponseInterface
	 */
	public function put(string $uri = '', array $options = []): ClientResponseInterface
	{
		return $this->request('PUT', $uri, $options);
	}

	/**
	 * Request with "DELETE" method.
	 * 
	 * @param string $uri
	 * @param array  $options
	 *
	 * @return ClientResponseInterface
	 */
	public function delete(string $uri = '', array $options = []): ClientResponseInterface
	{
		return $this->request('DELETE', $uri, $options);
	}

	/**
	 * Request with "CONNECT" method.
	 * 
	 * @param string $uri
	 * @param array  $options
	 *
	 * @return ClientResponseInterface
	 */
	public function connect(string $uri = '', array $options = []): ClientResponseInterface
	{
		return $this->request('CONNECT', $uri, $options);
	}

	/**
	 * Request with "OPTIONS" method.
	 * 
	 * @param string $uri
	 * @param array  $options
	 *
	 * @return ClientResponseInterface
	 */
	public function options(string $uri = '', array $options = []): ClientResponseInterface
	{
		return $this->request('OPTIONS', $uri, $options);
	}

	/**
	 * Request with "PATH" method.
	 * 
	 * @param string $uri
	 * @param array  $options
	 *
	 * @return ClientResponseInterface
	 */
	public function path(string $uri = '', array $options = []): ClientResponseInterface
	{
		return $this->request('PATH', $uri, $options);
	}

	/**
	 * Request with "TRACE" method.
	 * 
	 * @param string $uri
	 * @param array  $options
	 *
	 * @return ClientResponseInterface
	 */
	public function trace(string $uri = '', array $options = []): ClientResponseInterface
	{
		return $this->request('TRACE', $uri, $options);
	}
}

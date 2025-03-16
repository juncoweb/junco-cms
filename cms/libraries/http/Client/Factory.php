<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Client;

use Psr\Http\Message\StreamInterface;
use Junco\Http\Message\HttpFactory;
use Junco\Http\Client\ClientResponse;
use Junco\Filesystem\MimeHelper;

/**
 * Factory for internal use of the client library.
 */
class Factory extends HttpFactory
{
	// vars
	protected string $boundary;

	/**
	 * I rewrite the method to return a boosted response.
	 *
	 * {@inheritdoc}
	 */
	public function createResponse(int $code = 200, string $reasonPhrase = ''): clientResponseInterface
	{
		return new ClientResponse($code, [], null, '1.1', $reasonPhrase);
	}

	/**
	 * Create stream from multipart.
	 * 
	 * @param array $multipart
	 *  [name]*      The form field name (required).
	 *  [contents]*  StreamInterface|resource|string data contents (required).
	 *  [file]       Alternative contents, the contents will be filled with the contents of the file.
	 *  [headers][]  A custom headers (optional).
	 *  [filename]   The filename of the file being uploaded (optional).
	 *               If provided, will add a Content-Disposition header with "filename" parameter.
	 * 
	 * @throws \InvalidArgumentException
	 */
	public function createStreamFromMultipart(array $multipart = []): StreamInterface
	{
		$this->boundary = 'JuncoClientBoundary_' . base_convert(bin2hex(random_bytes(8)), 16, 36);
		$mimepart = '';

		foreach ($multipart as $part) {
			$part = array_merge([
				'name'		=> '',
				'contents'	=> '',
				'file'		=> '',
				'headers'	=> [],
				'filename'	=> ''
			], $part);

			$mimepart .= $this->createMimepart($part['name'], $part['contents'], $part['file'], $part['headers'], $part['filename']);
		}

		$mimepart .= "--{$this->boundary}--\r\n";

		/* header('Content-Type: text/plain');
		echo  $mimepart;
		exit(); */
		return $this->createStream($mimepart);
	}

	/**
	 * Get boundary
	 */
	public function getBoundary(): string
	{
		return $this->boundary;
	}

	/**
	 * @param string[] $headers
	 *
	 * @return array{0: StreamInterface, 1: string[]}
	 */
	protected function createMimepart(
		string 					$name,
		StreamInterface|string	$contents,
		string					$file,
		array					$headers,
		string					$filename
	): string {

		if (!$name) {
			throw new \InvalidArgumentException('The multipart stream requires the name');
		}
		if ($file) {
			$filename = $file;
			$contents = file_get_contents($file);
		}

		if ($contents instanceof StreamInterface) {
			if (!$filename) {
				$uri = $contents->getMetadata('uri');
				if (
					$uri
					&& is_string($uri)
					&& substr($uri, 0, 6) !== 'php://'
					&& substr($uri, 0, 7) !== 'data://'
				) {
					$filename = $uri;
				}
			}

			$contents = (string)$contents;
		}

		//throw new \InvalidArgumentException('The multipart stream contains malformed contents');

		// Set "Content-Disposition"
		if ($this->emptyHeader($headers, 'content-disposition')) {
			$headers['Content-Disposition'] = 'form-data; name="' . $name . '"';

			if ($filename ||  $filename === '0') {
				$headers['Content-Disposition'] .= '; filename="' . basename($filename) . '"';
			}
		}

		// Set "Content-Length"
		if ($this->emptyHeader($headers, 'content-length')) {
			$headers['Content-Length'] = (string)strlen($contents);
		}

		// Set "Content-Type"
		if ($this->emptyHeader($headers, 'content-type') && ($filename ||  $filename === '0')) {
			$headers['Content-Type'] = (new MimeHelper)->getType($filename) ?: 'application/octet-stream';
		}

		$mimepart = "--{$this->boundary}\r\n";
		foreach ($headers as $key => $value) {
			$mimepart .= "{$key}: {$value}\r\n";
		}
		$mimepart .= "\r\n";
		$mimepart .= $contents . "\r\n";

		return $mimepart;
	}

	/**
	 * Has header
	 * 
	 * @param string[] $headers
	 * @param string   $headerName
	 */
	protected function emptyHeader(array &$headers, string $headerName): bool
	{
		foreach ($headers as $name => $value) {
			if (strtolower($name) === $headerName) {
				if ($value) {
					return false;
				}
				unset($headers[$name]);
				return true;
			}
		}
		return true;
	}
}

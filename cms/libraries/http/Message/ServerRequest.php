<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Message;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Junco\Http\Message\Request;
use Junco\Http\Message\Stream;

/**
 * Representation of an incoming, server-side HTTP request.
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    // vars
    protected array $serverParams           = [];
    protected array $cookieParams           = [];
    protected array $queryParams            = [];
    protected array $uploadedFiles          = [];
    protected array $attributes             = [];
    protected array|object|null $parsedBody = null;

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
        ?string             $version = null,
        ?array              $serverParams = null
    ) {
        $stream ??= new Stream('php://input', 'r+');

        if ($serverParams) {
            $this->serverParams = $serverParams;
        }

        parent::__construct($method, $uri, $headers, $stream, $version);
    }

    /**
     * Retrieve server parameters.
     *
     * @return array
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * Retrieve cookies.
     * 
     * @return array
     */
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * Return an instance with the specified cookies.
     *
     * @param array $cookies Array of key/value pairs representing cookies.
     * 
     * @return static
     */
    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $new = clone $this;
        $new->cookieParams = $cookies;

        return $new;
    }

    /**
     * Retrieve query string arguments.
     * 
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * Return an instance with the specified query string arguments.
     * 
     * @param array $query Array of query string arguments, typically from $_GET.
     * 
     * @return static
     */
    public function withQueryParams(array $query): ServerRequestInterface
    {
        $new = clone $this;
        $new->queryParams = $query;

        return $new;
    }

    /**
     * Retrieve normalized file upload data.
     * 
     * @return array
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * Create a new instance with the specified uploaded files.
     *
     * @param array $uploadedFiles An array tree of UploadedFileInterface instances.
     * 
     * @return static
     * 
     * @throws \InvalidArgumentException if an invalid structure is provided.
     */
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        if (!$uploadedFiles) {
            return $this;
        }
        $this->normalizeUploadedFiles($uploadedFiles);

        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;

        return $new;
    }

    /**
     * Retrieve any parameters provided in the request body.
     *
     * @return null|array|object
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }


    /**
     * Return an instance with the specified body parameters.
     *
     * @param null|array|object $data The deserialized body data.
     * 
     * @return static
     * 
     * @throws \InvalidArgumentException
     */
    public function withParsedBody($data): ServerRequestInterface
    {
        if (
            !is_array($data)
            && !is_object($data)
            && null !== $data
        ) {
            throw new \InvalidArgumentException('The body parsed is not valid');
        }

        $new = clone $this;
        $new->parsedBody = $data;

        return $new;
    }

    /**
     * Retrieve attributes derived from the request.
     * 
     * @return array Attributes derived from the request.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Retrieve a single derived request attribute.
     * 
     * @param string $name The attribute name.
     * 
     * @param mixed $default Default value to return if the attribute does not exist.
     * 
     * @return mixed
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * Return an instance with the specified derived request attribute.
     * 
     * @param string $name The attribute name.
     * 
     * @param mixed $value The value of the attribute.
     * 
     * @return static
     */
    public function withAttribute(string $name, $value): ServerRequestInterface
    {
        $new = clone $this;
        $new->attributes[$name] = $value;

        return $new;
    }

    /**
     * Return an instance that removes the specified derived request attribute.
     *
     * @param string $name The attribute name.
     * 
     * @return static
     */
    public function withoutAttribute(string $name): ServerRequestInterface
    {
        if (!isset($this->attributes[$name])) {
            return $this;
        }

        $new = clone $this;
        unset($new->attributes[$name]);

        return $new;
    }

    /**
     * Normalize uploaded files from $_FILES or validate
     */
    protected function normalizeUploadedFiles(array &$uploadedFiles)
    {
        foreach ($uploadedFiles as $name => $file) {
            if (is_array($file)) {
                if (isset($file['tmp_name'])) {
                    $uploadedFiles[$name] = $this->normalizeUploadedFile($file);
                    continue;
                }
            } else {
                $file = [$file];
            }

            $this->validateUploadedFiles($file);
        }
    }

    /**
     * Normalize uploaded file
     */
    protected function normalizeUploadedFile(array $file)
    {
        if (is_array($file['tmp_name'])) {
            $files = [];
            foreach (array_keys($file['tmp_name']) as $i) {
                $files[] = new UploadedFile(
                    $file['tmp_name'][$i],
                    $file['size'][$i],
                    $file['error'][$i],
                    $file['name'][$i],
                    $file['type'][$i]
                );
            }
            return $files;
        }

        return new UploadedFile(
            $file['tmp_name'],
            $file['size'],
            $file['error'],
            $file['name'],
            $file['type']
        );
    }

    /**
     * Validate the structure in an uploaded files array.
     * 
     * @throws \InvalidArgumentException
     */
    protected function validateUploadedFiles(array $files)
    {
        foreach ($files as $file) {
            if (!$file instanceof UploadedFileInterface) {
                throw new \InvalidArgumentException('Invalid leaf in uploaded files structure');
            }
        }
    }
}

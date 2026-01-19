<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Server;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Junco\Http\Message\Uri;
use Junco\Http\Message\Stream;
use Junco\Http\Message\ServerRequest;

/**
 * Class for marshaling a request object from the current PHP environment.
 */
class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * Create a new server request.
     *
     * @param string $method The HTTP method associated with the request.
     * @param UriInterface|string $uri The URI associated with the request.
     * @param array $serverParams Array of SAPI parameters with which to seed the generated request instance.
     *
     * @return ServerRequestInterface
     */
    public function createServerRequest(string $method = '', $uri = null, array $serverParams = []): ServerRequestInterface
    {
        if (!$serverParams) {
            $serverParams = $_SERVER;
        }
        if (!$method) {
            $method = $_POST['__method'] ?? $serverParams['REQUEST_METHOD'] ?? '';
        }
        if (!$uri) {
            $uri = $this->getUri($serverParams);
        }

        $headers  = getallheaders();
        $stream   = new Stream('php://input', 'r+');
        $protocol = ltrim($serverParams['SERVER_PROTOCOL'] ?? '', 'HTP/');
        $request  = new ServerRequest($method, $uri, $headers, $stream, $protocol, $serverParams);

        if (!empty($_COOKIE)) {
            $request = $request->withCookieParams($_COOKIE);
        }
        if (!empty($_GET)) {
            $request = $request->withQueryParams($_GET);
        }
        if (!empty($_POST)) {
            $request = $request->withParsedBody($_POST);
        }
        if (!empty($_FILES)) {
            $request = $request->withUploadedFiles($_FILES);
        }

        return $request;
    }

    /**
     * Get Uri
     */
    protected function getUri(array $server): Uri
    {
        $parts = [];

        // scheme
        $parts['scheme'] = !empty($server['HTTPS']) && $server['HTTPS'] !== 'off' ? 'https' : 'http';

        // host & port
        if (!empty($server['HTTP_HOST'])) {
            if (preg_match('#^(.*)?\:(\d+)$#', $server['HTTP_HOST'], $match)) {
                $parts['host'] = $match[1];
                $parts['port'] = (int)$match[2];
            } else {
                $parts['host'] = $server['HTTP_HOST'];
            }
        } elseif (!empty($server['SERVER_NAME'])) {
            $parts['host'] = $parts['host'] = $server['SERVER_NAME'];
        } elseif (!empty($server['SERVER_ADDR'])) {
            $parts['host'] = $parts['host'] = $server['SERVER_ADDR'];
        }
        if (empty($parts['port']) && !empty($server['SERVER_PORT'])) {
            $parts['port'] = $server['SERVER_PORT'];
        }

        // path & query # fragment
        if (isset($server['REQUEST_URI'])) {
            $partial = explode('?', $server['REQUEST_URI'], 2);
            $parts['path'] = $partial[0];
            if (isset($partial[1])) {
                $parts['fragment'] = $partial[1];
            }
            $partial = explode('?', $parts['path'], 2);
            $parts['path'] = $partial[0];
            if (isset($partial[1])) {
                $parts['query'] = $partial[1];
            }
        }
        if (!isset($parts['query']) && isset($server['QUERY_STRING'])) {
            $parts['query'] = ltrim($server['QUERY_STRING'], '?');
        }

        return new Uri($parts);
    }
}

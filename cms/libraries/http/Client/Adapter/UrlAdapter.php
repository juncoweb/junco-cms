<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Client\Adapter;

use Psr\Http\Message\RequestInterface;
use Junco\Http\Client\ClientResponseInterface;
use Junco\Http\Client\Exception\NetworkException;

class UrlAdapter extends AdapterAbstract
{
    /**
     * Constructor
     */
    public function __construct()
    {
        if (!ini_get('allow_url_fopen')) {
            throw new \Exception(_t('Your server does not allow remote query.'));
        }
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
        $uri        = $request->getUri();
        $method        = $request->getMethod();
        $headers    = $request->getHeaders();

        $http = [
            'method' => $method,
            'protocol_version' => $request->getProtocolVersion(),
            'ignore_errors' => true
        ];

        if ($headers) {
            foreach ($headers as $name => $values) {
                $headers[$name] = $name . ': ' . implode(',', $values) . PHP_EOL;
            }
            $http['header'] = implode($headers);
        }

        if ($method != 'GET') {
            $content = (string)$request->getBody();

            if ($content) {
                $http['content'] = $content;
            }
        }
        $context = stream_context_create(['http' => $http]);
        $content = @file_get_contents($uri, 'r', $context);

        if ($content === false) {
            $message = error_get_last()['message'] ?? sprintf('file_get_contents(%s): Failed to open stream', $uri);
            throw new NetworkException($message, $request);
        }

        //
        $builder = $this->createResponseBuilder();
        $builder->setHeadersFromArray($http_response_header);
        if ($content) {
            $builder->setBody($content);
        }

        return $builder->getResponse();
    }
}

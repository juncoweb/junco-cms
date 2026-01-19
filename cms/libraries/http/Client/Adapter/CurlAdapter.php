<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Client\Adapter;

use Psr\Http\Message\RequestInterface;
use Junco\Http\Client\ClientResponseInterface;
use Junco\Http\Client\Exception\NetworkException;

class CurlAdapter extends AdapterAbstract
{
    /**
     * Constructor
     */
    public function __construct()
    {
        if (!function_exists('curl_init')) {
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
        $protocol    = $request->getProtocolVersion();
        $method        = $request->getMethod();

        $ch = curl_init();
        $options[CURLOPT_URL] = $uri;
        $options[CURLOPT_PROTOCOLS] = CURLPROTO_HTTP | CURLPROTO_HTTPS;
        $options[CURLOPT_HTTP_VERSION] = $this->getProtocolVersion($protocol);
        $options[CURLOPT_HTTPHEADER] = [];

        // callback
        $builder = $this->createResponseBuilder();
        $options[CURLOPT_HEADERFUNCTION] = function ($ch, $data) use ($builder) {
            $str = trim($data);
            if ($str !== '') {
                if (stripos($str, 'http/') === 0) {
                    $builder->setStatus($str);
                } else {
                    $builder->setHeader($str);
                }
            }

            return strlen($data);
        };

        $options[CURLOPT_WRITEFUNCTION] = function ($ch, $data) use ($builder) {
            return $builder->getResponse()->getBody()->write($data);
        };


        /*if (parse_url($url, PHP_URL_SCHEME) =='https') {
			$options[CURLOPT_SSL_VERIFYPEER] = true;
			$options[CURLOPT_SSL_VERIFYHOST] = 2;
			$options[CURLOPT_CAINFO] = $this->cacert;
		}*/
        //$options[CURLOPT_SSL_VERIFYPEER] = false;

        // method
        if ($method == 'HEAD') {
            $options[CURLOPT_NOBODY] = true; // *Request method is then set to HEAD.
        } elseif ($method !== 'GET') {
            $options[CURLOPT_CUSTOMREQUEST] = $method;
        }

        // body
        if (!in_array($method, ['GET', 'HEAD', 'TRACE'])) {
            // download file
            /* if (isset($options[CURLOPT_FILE])) {
				$tmp = tmpfile();
				$options[CURLOPT_WRITEHEADER] = $tmp;
			} else {
				$options[CURLOPT_RETURNTRANSFER] = 1;
				$options[CURLOPT_HEADER] = 1;
			} */

            $body = $request->getBody();
            $size = $body->getSize();

            if ($size !== 0) {
                // Send the body as a string if the size is less than 1MB
                if ($size !== null && $size < (1024 * 1024)) {
                    $options[CURLOPT_POSTFIELDS] = (string)$body;

                    if ($request->hasHeader('Content-Length')) {
                        $length = strlen($options[CURLOPT_POSTFIELDS]);
                        $request = $request->withHeader('Content-Length', $length);
                    }
                } else {
                    $options[CURLOPT_UPLOAD] = true;
                    if ($size !== null) {
                        $options[CURLOPT_INFILESIZE] = $size;
                    }
                    if ($body->isSeekable()) {
                        $body->rewind();
                    }
                    $options[CURLOPT_READFUNCTION] = static function ($ch, $fd, $length) use ($body) {
                        return $body->read($length);
                    };

                    if ($request->hasHeader('Content-Length')) {
                        $request = $request->withHeader('Content-Length', 0);
                    }
                }

                if ($request->hasHeader('Expect')) {
                    /** @see: https://www.php.net/manual/en/function.curl-setopt.php#82418 */
                    $request = $request->withoutHeader('Expect');
                    $options[CURLOPT_HTTPHEADER][] = 'Expect:';
                }
            } elseif ($method === 'PUT' || $method === 'POST') {
                /** @see https://datatracker.ietf.org/doc/html/rfc7230#section-3.3.2 */
                if (!$request->hasHeader('Content-Length')) {
                    $conf[CURLOPT_HTTPHEADER][] = 'Content-Length: 0';
                }
            }
        }

        // headers
        foreach ($request->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $options[CURLOPT_HTTPHEADER][] = $name . ': ' . $value;
            }
        }

        // execute
        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);

        if ($result === false) {
            throw new NetworkException(curl_error($ch), $request);
        }

        return $builder->getResponse();
    }

    /**
     * Return cURL constant for specified HTTP version.
     *
     * @param string $version	The request protocol version.
     *
     * @return int
     */
    protected function getProtocolVersion(string $version): int
    {
        switch ($version) {
            case '1.0':
                return CURL_HTTP_VERSION_1_0;
            case '1.1':
                return CURL_HTTP_VERSION_1_1;
            case '2.0':
                return CURL_HTTP_VERSION_2_0;
        }

        return CURL_HTTP_VERSION_NONE;
    }
}

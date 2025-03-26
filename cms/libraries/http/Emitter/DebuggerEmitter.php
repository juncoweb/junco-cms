<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Emitter;

use Psr\Http\Message\ResponseInterface;

/**
 * Sapi HTTP response emitter.
 */
class DebuggerEmitter implements EmitterInterface
{
    /**
     * Emits a response for a PHP SAPI environment.
     * 
     * @param ResponseInterface $response
     */
    public function emit(ResponseInterface $response)
    {
        header('Content-Type: text/plain');

        $this->emitStatusLine($response);
        $this->emitHeaders($response);
        $this->emitBody($response);
    }

    /**
     * Emit headers
     *
     * @param ResponseInterface $response
     */
    private function emitHeaders(ResponseInterface $response): void
    {
        foreach ($response->getHeaders() as $name => $values) {
            $name = ucwords(strtolower($name), '-');

            foreach ($values as $value) {
                echo "$name: $value";
            }
        }
    }

    /**
     * Emit the status line.
     */
    protected function emitStatusLine(ResponseInterface $response): void
    {
        $protocolVersion = $response->getProtocolVersion();
        $statusCode = $response->getStatusCode();
        $reasonPhrase = $response->getReasonPhrase();

        if ($reasonPhrase) {
            $reasonPhrase = ' ' . $reasonPhrase;
        }

        echo "HTTP/$protocolVersion $statusCode{$reasonPhrase}";
    }

    /**
     * Emit the message body.
     */
    protected function emitBody(ResponseInterface $response): void
    {
        echo "\r\n" . $response->getBody();
    }
}

<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Emitter;

use Psr\Http\Message\ResponseInterface;

/**
 * Sapi HTTP response emitter.
 */
class SapiEmitter implements EmitterInterface
{
    /**
     * Emits a response for a PHP SAPI environment.
     * 
     * @param ResponseInterface $response
     */
    public function emit(ResponseInterface $response)
    {
        $this->verify();
        $this->emitHeaders($response);
        $this->emitStatusLine($response);
        $this->emitBody($response);
    }

    /**
     * Verify
     */
    protected function verify(): void
    {
        if (headers_sent()) {
            //var_dump(headers_list());
            throw new \Error('Unable to emit response; headers already sent.');
        }

        if (ob_get_level() > 0 && ob_get_length() > 0) {
            throw new \Error('Unable to emit response; output has been emitted previously.');
        }
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
            $replace = $name !== 'Set-Cookie';

            foreach ($values as $value) {
                header("$name: $value", $replace);
                $replace = false;
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

        header("HTTP/$protocolVersion $statusCode{$reasonPhrase}", true, $statusCode);
    }

    /**
     * Emit the message body.
     */
    protected function emitBody(ResponseInterface $response): void
    {
        echo $response->getBody();
    }
}

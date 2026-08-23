<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Debugger;

use Junco\Http\Emitter\SapiEmitter;
use Junco\Http\Exception\HttpThrowableInterface;
use Psr\Http\Message\ResponseInterface;
use Error;
use Responder;
use Throwable;

class HttpThrowableHandler extends ThrowableHandler
{
    /**
     * Emit
     * 
     * @param Throwable $e
     * 
     * @return void
     */
    public function emit(Throwable $e): void
    {
        try {
            $response = $this->getResponse($e, true);
            (new SapiEmitter)->emit($response);
            die;
        } catch (Throwable $x) {
        }

        http_response_code(500);
        die(sprintf('%d - %s', $e->getCode(), $e->getTraceAsString()));
    }

    /**
     * Get
     * 
     * @param Throwable $e
     * 
     * @return ResponseInterface
     */
    public function getResponse(Throwable $e, bool $severe = false): ResponseInterface
    {
        $statusCode = 0;

        if ($e instanceof HttpThrowableInterface) {
            $statusCode = $e->getStatusCode();

            if (!$severe && $statusCode == 403) {
                $severe = true; // Force the basic template.
            }
        }

        $message = $e instanceof Error
            ? $this->handleThrowableError($e, $statusCode)
            : $e->getMessage();

        if (!$message) {
            $message = $this->getMessageFromCode($statusCode);
        }

        $code = (int)$e->getCode();

        return Responder::get($severe)->responseWithMessage($message, $statusCode, $code);
    }
}

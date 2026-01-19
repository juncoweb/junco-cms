<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use \Filter;
use \System;

class SecurityMiddleware implements MiddlewareInterface
{
    // vars
    protected $method = null;

    /**
     * Process an incoming server request.
     */
    public function __construct(?int $method = null)
    {
        $this->method = $method ?? INPUT_POST;
    }

    /**
     * Process an incoming server request.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->verify()) {
            return System::getOutput()
                ->responseWithMessage(_t('The security token has expired. Refresh the page.'))
                ->withStatus(403, 'Invalid CSRF Token');
        }

        return $handler->handle($request);
    }

    /**
     * Verify
     */
    public function verify(): bool
    {
        $token_key = config('form.csrf_token_key');
        $request_token = Filter::input($this->method, $token_key);

        if ($request_token) {
            $session_token = session()->get($token_key);

            if ($session_token && $session_token == $request_token) {
                return true;
            }
        }

        return false;
    }
}

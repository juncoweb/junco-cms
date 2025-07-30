<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Authentication\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class BounceMiddleware implements MiddlewareInterface
{
    //
    protected int $code = 404;

    /**
     * Constructor
     */
    public function __construct(int $code = 0)
    {
        if ($code === 403) {
            $this->code = $code;
        }
    }

    /**
     * Process an incoming server request.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        alert($this->code);
        //return $handler->handle($request);
    }
}

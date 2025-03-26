<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Router\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Junco\Router\Runner\Runner;
use \Router;

class RouterMiddleware implements MiddlewareInterface
{
    /**
     * Process an incoming server request.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface|Runner $handler): ResponseInterface
    {
        $router = new Router($request->getMethod(), $request->getQueryParams());
        $handler->setRouter($router);
        //
        $router->initialize();
        $router->getArguments(function (array $args) use (&$request) {
            // Should be added as attributes and NOT as query params :\
            $request = $request->withQueryParams(
                array_merge($request->getQueryParams(), $args)
            );
        });

        return $handler->handle($request);
    }
}

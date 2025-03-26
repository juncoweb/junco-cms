<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Install\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use \Exception;

class UserTableExistsMiddleware implements MiddlewareInterface
{
    /**
     * Process an incoming server request.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $has = db()->getSchema()->tables()->has('users');

        if (!$has) {
            throw new Exception(sprintf('MiddlewareError: %s', _t('The installer requires the user table.')));
        }

        return $handler->handle($request);
    }
}

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Extensions\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class SecurityMiddleware implements MiddlewareInterface
{
	/**
	 * Process an incoming server request.
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$is_demo = defined('IS_DEMO') ? constant('IS_DEMO') : false;

		if ($is_demo) {
			throw new \Exception(_t('This task is not allowed in demos.'));
		}

		return $handler->handle($request);
	}
}

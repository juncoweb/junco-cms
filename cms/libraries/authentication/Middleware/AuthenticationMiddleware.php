<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Authentication\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
    protected array $labels = [];

    /**
     * Constructor
     */
    public function __construct(...$labels)
    {
        foreach ($labels as $label_id) {
            if (is_numeric($label_id)) {
                $this->labels[] = $label_id;
            } else {
                $this->labels[] = constant($label_id);
            }
        }
    }

    /**
     * Process an incoming server request.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        curuser()->authenticate(...$this->labels);

        return $handler->handle($request);
    }
}

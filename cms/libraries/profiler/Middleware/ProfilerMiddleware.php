<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Profiler\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Junco\Http\Message\HttpFactory;

class ProfilerMiddleware implements MiddlewareInterface
{
    /**
     * Process an incoming server request.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $profiler = app('profiler');
        $profiler->mark('At the start of the profiler middleware');
        $format = router()->getFormat();

        // add javascript control
        if ($format == 'template') {
            app('assets')->domready('JsConsole.load()');
        }

        // response
        $response = $handler->handle($request);

        if ($format == 'template' || $format == 'text') {
            $content = $response->getBody();
            $console = $profiler->render(true);

            if ($format == 'template') {
                $content = str_replace('</body>', '<console><!--{profiler} ' . $console . '--></console></body>', $content);
            } else {
                $content .= '<!--{profiler} ' . $console . '-->';
            }

            $response = $response->withBody((new HttpFactory)->createStream($content));
        }

        return $response;
    }
}

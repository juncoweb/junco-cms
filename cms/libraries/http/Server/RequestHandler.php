<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Http\Server;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Junco\Debugger\ThrowableHandler;

/**
 * Handles a server request and produces a response.
 */
class RequestHandler implements RequestHandlerInterface
{
    // vars
    protected $middlewares = [];
    protected $fallbackHandler;

    /**
     * Constructor
     * 
     * @param RequestHandlerInterface $fallbackHandler
     */
    public function __construct(RequestHandlerInterface $fallbackHandler)
    {
        $this->fallbackHandler = $fallbackHandler;
    }

    /**
     * Add middleware to the queue.
     * 
     * @param string $middleware
     * @param array  ...$args
     */
    public function add(string $middleware, ...$args): void
    {
        $this->middlewares[] = [$middleware, $args];
    }

    /**
     * Handles a request and produces a response.
     *
     * @param ServerRequestInterface $request
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            if (!$this->middlewares) {
                return $this->fallbackHandler->handle($request);
            }

            $middleware = array_shift($this->middlewares);
            $middleware = $this->getMiddleware($middleware[0], $middleware[1]);

            return $middleware->process($request, $this);
        } catch (\Throwable $e) {
            return $this->exceptionHandler($e);
        }
    }

    /**
     * Get new instance of the middleware.
     *
     * @param string $className
     * @param array  $args
     *
     * @return MiddlewareInterface
     */
    protected function getMiddleware(string $className, array $args): MiddlewareInterface
    {
        if (strpos($className, '\\') === false) {
            if (strpos($className, '.') === false) {
                $className = ucfirst($className);
                $extension = $className;
            } else {
                $parts     = array_map('ucfirst', explode('.', $className));
                $extension = array_shift($parts);
                $className = implode($parts);
            }
            $className = 'Junco\\' . $extension . '\\Middleware\\' . $className . 'Middleware';
        }

        if ($args) {
            $reflector = new \ReflectionClass($className);
            return $reflector->newInstanceArgs($args);
        }

        return new $className;
    }

    /**
     * Exception handler
     *
     * @param \Throwable $e 
     *
     * @return ResponseInterface
     */
    public function exceptionHandler(\Throwable $e): ResponseInterface
    {
        return (new ThrowableHandler)->getResponse($e);
    }
}

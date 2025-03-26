<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Router\Runner;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Junco\Http\Message\HttpFactory;
use Junco\Http\Server\RequestHandler;
use Junco\Mvc\Controller;
use Junco\Container\Container;
use \Router;
use \Closure;

/**
 * Handles a server request and produces a response.
 */
class Runner extends RequestHandler
{
    // vars
    protected Container    $container;
    protected ?Router    $router                    = null;
    protected bool        $isInspectable            = false;
    protected int        $step                    = -1;
    protected Closure|array|false $controller    = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->container = Container::getInstance();
    }

    /**
     * Set
     */
    public function setRouter(Router $router)
    {
        $this->container->set('router', $router);
        $this->router = $router;
        $this->step   = 0;
    }

    /**
     * Handles a request and produces a response.
     *
     * @param ServerRequestInterface $request
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            // I save any new request.
            $this->container->set('request', $request, true);

            //
            if (!$this->middlewares) {
                if ($this->step === 0) {
                    $this->findMiddlewareInAccessPoint();
                    if ($this->middlewares) {
                        return $this->handle($request);
                    }
                }
                if ($this->step === 1) {
                    $this->findMiddlewareInController();
                    if ($this->middlewares) {
                        return $this->handle($request);
                    }
                }
                if ($this->controller === false) {
                    throw new \SystemException(404);
                }
                while (!$response = $this->runController()) {
                    return $this->handle($request);
                }

                return $response;
            }

            $middleware = array_shift($this->middlewares);
            $middleware = $this->getMiddleware($middleware[0], $middleware[1]);

            return $middleware->process($request, $this);
        } catch (\Throwable $e) {
            return $this->exceptionHandler($e);
        }
    }

    /**
     * Run through the access point.
     */
    protected function findMiddlewareInAccessPoint()
    {
        $this->step = 1;
        $this->router->getMiddlewares($this);
    }

    /**
     * Run through the controller.
     */
    protected function findMiddlewareInController()
    {
        $this->step          = 2;
        $this->controller    = $this->router->getController();

        if (is_array($this->controller)) {
            if (is_string($this->controller[0])) {
                $this->controller[0] = $this->container->get($this->controller[0]);
            }

            if ($this->controller[0] instanceof Controller) {
                $this->controller[0]->getMiddlewares($this);
                $this->isInspectable = true;
            }
        }
    }

    /**
     * Run through the executable.
     *
     * @return ResponseInterface|false
     */
    protected function runController(): ResponseInterface|false
    {
        ob_start();

        $response = $this->container->call($this->controller);

        if ($response === true && $this->isInspectable) {
            // Middleware has been loaded.
            return false;
        }

        if ($response instanceof ResponseInterface) {
            if ($this->getOutputBuffer()) {
                // Response originators MUST manage the output buffer.
                app('logger')->alert('There is output buffering after the response has been created');
            }

            return $response;
        }

        return $this->createResponse($response);
    }

    /**
     * Get
     *
     * @return string
     */
    protected function getOutputBuffer(): string
    {
        $buffer = '';

        while (ob_get_level()) {
            $buffer = ob_get_clean() . $buffer;
        }

        return $buffer;
    }

    /**
     * Create response
     * 
     * @param mixed  $content
     * @param string $buffer
     *
     * @return ResponseInterface
     */
    protected function createResponse(mixed $content): ResponseInterface
    {
        $factory = new HttpFactory;
        $response = $factory->createResponse();

        if (is_array($content)) {
            if ($this->getOutputBuffer()) {
                app('logger')->alert('The Runner is ignoring an output buffer to create a json');
            }

            $response = $response->withHeader('Content-type', 'application/json; charset=utf-8');
            $content = json_encode($content);
        } else {
            if ($content === 1) {
                $content = '';
            }

            if ($buffer = $this->getOutputBuffer()) {
                $content = $buffer . $content;
            }
        }

        return $response->withBody($factory->createStream($content));
    }
}

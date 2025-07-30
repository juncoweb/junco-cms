<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Mvc;

use Junco\Debugger\ThrowableHandler;
use Junco\Http\Exception\HttpError;
use Junco\Http\Exception\HttpException;
use Junco\Http\Server\RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class Controller
{
    // vars
    private ?RequestHandler $handler      = null;
    private array           $middlewares  = [];
    private array           $traces       = [];

    /**
     * Middleware
     * 
     * @param ...$middlewares
     * 
     * @return bool
     */
    final public function middleware(...$middlewares): bool
    {
        if (!$middlewares) {
            return false;
        }
        $middlewares = $this->filterMiddlewares($middlewares);

        if ($this->handler && $middlewares) {
            $key = implode('$', array_keys($middlewares));

            if (in_array($key, $this->traces)) {
                return false;
            }

            $this->traces[] = $key;
            $this->middlewares = $middlewares;
            $this->getMiddlewares();

            return true;
        }

        $this->middlewares = array_merge($this->middlewares, $middlewares);
        return false;
    }

    /**
     * Middleware
     * 
     * @param ...$middlewares
     */
    final public function filterMiddlewares($middlewares): array
    {
        if (is_array($middlewares[0])) {
            $middlewares = $middlewares[0];

            foreach ($middlewares as $middleware => $args) {
                if (!is_array($args)) {
                    $middlewares[$middleware] = [$args];
                }
            }

            return $middlewares;
        }

        return array_fill_keys($middlewares, []);
    }

    /**
     * This is a middleware, only a bit more important.
     * 
     * @param int ...$label_id
     * 
     * @return bool
     */
    final public function authenticate(int ...$label_id): bool
    {
        if ($this->handler) {
            return $this->middleware(['authentication' => $label_id]);
        }

        $this->middlewares['authentication'] = $label_id;
        return true;
    }

    /**
     * Middlewares
     * 
     * @param ?RequestHandler $handler
     * 
     * @return void
     */
    final public function getMiddlewares(?RequestHandler $handler = null): void
    {
        if ($handler) {
            $this->handler = $handler;
        }

        foreach ($this->middlewares as $middleware => $args) {
            $this->handler->add($middleware, ...$args);
        }

        $this->middlewares = [];
    }

    /**
     * Include a view and pass data to it.
     * 
     * @param ?string $path
     * @param ?array $data
     * 
     * @return string|array|ResponseInterface
     */
    final protected function view(?string $__view = null, ?array $data = null): mixed
    {
        $data and extract($data);
        return include $this->getViewFile($__view);
    }

    /**
     * Get View File
     */
    final protected function getViewFile(?string $path = null): string
    {
        $component = '';

        if ($path) {
            $path = explode('.', $path);

            if (count($path) > 1) {
                $component = array_shift($path);
                $filename  = array_pop($path);
            }
        }

        if (!$component) {
            $_path        = explode('_', preg_replace('#[A-Z]#', '_$0', lcfirst(substr(static::class, 0, -10))));
            $component    = lcfirst(array_splice($_path, 1, 1)[0]);

            if ($path) {
                $path     = array_merge($_path, $path);
                $filename = array_pop($path);
            } else {
                $trace    = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2];
                $path     = $_path;
                $filename = ucfirst($trace['function']);
            }
        }

        $filepath = SYSTEM_ABSPATH . 'app/' . $component . '/views/' . strtolower(implode('/', $path)) . '/' . $filename . '.php';

        if (!is_file($filepath)) {
            throw new HttpException(500, sprintf('Error! The view has not been found: %s.', $filepath));
        }

        return $filepath;
    }

    /**
     * Wrapper
     * 
     * @param callable $fn
     * 
     * @return Psr\Http\Message\ResponseInterface | Junco\Console\Output\OutputInterface
     */
    final protected function wrapper(callable $fn): mixed
    {
        try {
            $result = $fn();

            if ($result === null) {
                $result = new Result(200, _t('The task has been completed successfully.'), 1);
            } elseif ($result instanceof ResponseInterface) { // legacy
                app('logger')->notice('The controller wrapper should not return a ResponseInterface object.');
                return $result;
            } elseif (!$result instanceof Result) {
                app('logger')->notice('The controller wrapper should return a Result object.');
                $result = $this->getLegacyResult($result); // legacy
            }

            return \System::getOutput()->responseWithMessage($result);
        } catch (HttpException | HttpError $e) { // new features
            return (new ThrowableHandler)->getResponse($e);
        } catch (\Exception $e) { // legacy
            return \System::getOutput()->responseWithMessage($e->getMessage(), 422, $e->getCode());
        }
    }

    /**
     * Get
     */
    protected function getLegacyResult(mixed $result): Result
    {
        $code    = 1;
        $message = '';
        $data    = null;

        if (is_numeric($result)) {
            $code = $result;
        } elseif (is_array($result)) {
            $message = $result[0] ?? null;
            $code    = $result[1] ?? 0;
            $data    = $result[2] ?? null;
        } else {
            $message = $result;
        }

        if ($message === '') {
            $message = _t('The task has been completed successfully.');
        } elseif (!$message) {
            $message = '';
        }

        return new Result(200, $message, $code, $data);
    }
}

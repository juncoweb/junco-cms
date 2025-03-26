<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Router;

use Closure;
use Error;
use Router;

/**
 * Routes
 */
class Routes
{
    // route
    protected string  $method                = '';
    protected array   $queryParams            = [];
    protected array   $routes                = [];
    protected array   $arguments            = [];
    protected int     $counter                = 0;
    protected string  $route_baseid            = '';
    protected ?string $current_route_baseid    = null;

    /**
     * Constructor
     * 
     * @param string $method
     * @param array  $queryParams
     */
    public function __construct(string $method, array $queryParams)
    {
        $this->method        = $method;
        $this->queryParams    = $queryParams;
    }

    /**
     * Import a route file
     * 
     * @param string $access_point
     * @param string $component
     * 
     * @return ?self
     */
    public function import(string $access_point, string $component): ?self
    {
        $file = SYSTEM_ABSPATH . sprintf('app/%s/routes/%sRoutes.php', $component, ucfirst($access_point));

        if (!is_file($file)) {
            return null;
        }

        $this->setBaseId($access_point, $component);
        system_import($file, [
            'router' => $this, // @deprecated
            'routes' => $this
        ]);

        return $this;
    }

    /**
     * Sets the base id
     * 
     * @param string $access_point
     * @param string $component
     * 
     * @return void
     */
    public function setBaseId(string $access_point, string $component): void
    {
        $this->route_baseid = "#{$access_point}.{$component}";

        // I save the route baseid for use leter a url without baseid: url('#url-id')
        $this->current_route_baseid ??= $this->route_baseid;
    }

    /**
     * Get Controller
     * 
     * @param string $curRoute
     * 
     * @return Closure|array|string|null
     */
    public function getController(string $curRoute): Closure|array|string|null
    {
        foreach ($this->routes as $route) {
            $this->prepare($route);

            // I looking for a match
            if (preg_match($route['pattern'], $curRoute, $match)) {
                // I verify the method
                if (!in_array($this->method, $route['method'])) {
                    continue;
                }

                // I check if it asks for a parameter
                if ($route['paramNames'] && !$this->issetParam($route['paramNames'])) {
                    continue;
                }

                // I save the route arguments.
                foreach ($match as $var_name => $value) {
                    if (isset($route['keys'][$var_name])) {
                        $this->arguments[$route['keys'][$var_name]] = $value;
                    }
                }

                return $route['callback'];
            }
        }

        return null;
    }

    /**
     * Returns the arguments found in the path of the url.
     * 
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Records a route
     * 
     * @param string   						$uri
     * @param string|array|callable|null	$callback
     * @param string   						$id
     */
    public function route(
        string                        $uri,
        string|array|callable|null    $callback,
        string                        $id = ''
    ) {
        $this->addRoute($uri, $callback, $id);
    }

    /**
     * Records a route with args
     * 
     * @param string 						$uri
     * @param array							$paramNames
     * @param string|array|callable|null	$callback
     * @param string						$id
     */
    public function routeWith(
        string                        $uri,
        array                        $paramNames,
        string|array|callable|null    $callback,
        string                        $id = ''
    ) {
        $this->addRoute($uri, $callback, $id, $paramNames);
    }

    /**
     * Adds a routing record
     * 
     * @param string 						$uri
     * @param string|array|callable|null	$callback
     * @param string						$id
     * @param array							$paramNames
     */
    protected function addRoute(
        string                        $uri,
        string|array|callable|null    $callback,
        string                        $id,
        array                        $paramNames = []
    ) {
        $uri    = explode(' ', trim($uri), 2);
        $method = '';

        if (empty($uri[1])) {
            $uri    = $uri[0];
        } else {
            $method = strtoupper($uri[0]);
            $uri    = ltrim($uri[1]);
        }

        $route_id = $id
            ? $this->route_baseid . $id
            : $this->counter++;

        if (isset($this->routes[$route_id])) {
            throw new Error('The route id already exists');
        }

        $this->routes[$route_id] = [
            'method'        => $method ? explode('|', $method) : ['GET'],
            'uri'            => $uri,
            'paramNames'    => $paramNames,
            'callback'        => $callback,
        ];
    }

    /**
     * Prepare
     * 
     * @param array $route
     * 
     * @return void
     */
    protected function prepare(array &$route): void
    {
        $uri = $route['uri'];
        $pattern = '#'
            . '/(?:(?<prefix>[^/]+)=/)?'
            . '\{(?<name>.+?)(?:\:(?<type>.+?))?(?<optional>\?)?\}'
            . '#';
        $route['keys'] = [];

        if (preg_match_all($pattern, $route['uri'], $matches, PREG_SET_ORDER)) {
            $replaces = [];

            foreach ($matches as $i => $match) {
                $var_find    = $match[0];
                $var_prefix = $match['prefix'] ?? '';
                $var_name    = $match['name'];
                $var_type    = $match['type'] ?? '';
                $optional    = $match['optional'] ?? '';
                $var_key     = "var{$i}";

                switch ($var_type) {
                    case 'id':
                        $var_replace = '[0-9]+';
                        break;
                    case 'int':
                        $var_replace = '-?[0-9]+';
                        break;
                    default:
                    case 'string':
                        $var_replace = '[^/]+?';
                        break;
                    case '*':
                        $var_replace = '.+?';
                        break;
                }

                $replaces[$var_find] = '(?:' . ($var_prefix ? '/' . $var_prefix : '') . '/(?<' . $var_key . '>' . $var_replace . '))' . $optional;
                $route['keys'][$var_key] = $var_name;
            }

            $uri = strtr($route['uri'], $replaces);
        }

        $route['pattern'] = '#^' . $uri . '$#';
    }

    /**
     * Get a route from an id
     * 
     * @param string $route_id
     * 
     * @return ?string
     */
    public function getRouteFromId(string $route_id): ?string
    {
        if (isset($this->routes[$route_id])) {
            return $this->routes[$route_id]['uri'];
        }

        if (isset($this->routes[$this->current_route_baseid . $route_id])) {
            return $this->routes[$this->current_route_baseid . $route_id]['uri'];
        }

        return $this->findRouteImportingComponent($route_id);
    }

    /**
     * Isset Param
     * 
     * @param array $paramNames
     */
    protected function issetParam(array $paramNames)
    {
        foreach ($paramNames as $param) {
            if (!isset($this->queryParams[$param])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get
     * 
     * @param string $route_id
     * 
     * @return ?string
     */
    protected function findRouteImportingComponent(string $route_id): ?string
    {
        if (!preg_match('/^#([a-z0-9]*?)\.([a-z0-9]*?)#/', $route_id, $match)) {
            return null;
        }

        if (!$this->import($match[1], $match[2])) {
            return null;
        }

        return $this->routes[$route_id]['uri'] ?? null;
    }
}

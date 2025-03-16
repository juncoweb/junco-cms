<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Router\Routes;
use Junco\Http\Server\RequestHandler;

/**
 * Router
 * 
 * @require <Request> or <Input>
 */
class Router
{
	// vars
	protected array  $route	 					= [];
	protected string $orig_route				= '';
	protected ?array $queryParams				= null;
	protected ?array $url_lang 					= null;
	protected string $access_point				= 'front';
	protected string $component					= '';
	protected string $deepcomponent				= '';
	protected string $fullcomponent				= '';
	protected string $task						= '';
	protected string $format					= 'template';
	protected Closure|array|false $controller	= false;
	protected Routes $routes;
	// config
	protected string $route_key;
	protected string $format_key;
	protected array  $access_points;
	protected bool   $use_rewrite;
	protected array  $route_replaces;
	//
	protected string $site_url;
	protected string $site_baseurl;

	/**
	 * Constructor
	 * 
	 * @param string $method
	 * @param array  $queryParams
	 */
	public function __construct(string $method, array $queryParams)
	{
		$config					= config('router');
		$this->route_key		= $config['router.route_key'] ?? 'goto';
		$this->format_key		= $config['router.format_key'];
		$this->access_points	= $config['router.access_points'];
		$this->route_replaces	= $config['router.route_replaces'] ?: [];
		$this->use_rewrite		= $config['router.use_rewrite'];

		$config					= config('site');
		$this->site_url			= $config['site.url'];
		$this->site_baseurl		= $config['site.baseurl'];

		//
		$this->extractRouteAndFormat($method, $queryParams);
		$this->queryParams = $queryParams;

		// Initialize routes
		$this->routes = new Routes($method, $queryParams);
	}

	/**
	 * Lookup Language
	 * 
	 * @param array $availables
	 * 
	 * @return ?string
	 */
	public function lookupLanguage(array $availables): ?string
	{
		if ($this->route && in_array($this->route[0], $availables)) {
			return array_shift($this->route);
		}

		return null;
	}

	/**
	 * Extract
	 * 
	 * @param string $method
	 * @param array  &$queryParams
	 * 
	 * @return void
	 */
	protected function extractRouteAndFormat(string $method, array &$queryParams): void
	{
		if (array_key_exists($this->route_key, $queryParams)) {
			$this->orig_route = $queryParams[$this->route_key];
			unset($queryParams[$this->route_key]);
		}

		if ($this->orig_route) {
			$this->route = array_values(array_filter(explode('/', $this->orig_route)));
		}

		if ($method == 'INPUT') {
			$this->format = 'console';
		} else {
			$format	= $queryParams[$this->format_key] ?? null;

			if ($format && preg_match('/^[a-z]+$/', $format)) {
				$this->format = $format;
			}
		}
	}

	/**
	 * Resolve route
	 * 
	 * @return void
	 */
	protected function resolveRoute(): void
	{
		// I am looking for the access point
		if ($this->route && in_array($this->route[0], $this->access_points)) {
			$this->access_point = array_shift($this->route);
		}

		// I am looking for the component and the task
		if ($this->route) {
			$component				= explode('.', array_shift($this->route), 2);
			$this->deepcomponent	= $component[1] ?? '';
			$this->component		= $component[0];
			if ($this->route) {
				$this->task 		= implode('/', $this->route);
			}
		} elseif ($this->access_point == 'front') {
			$this->component = config('router.front_default_component');
		} elseif ($this->access_point == 'console') {
			$this->component = 'console';
		} else {
			$this->component = 'dashboard';
		}

		// I replace the name of the components and tasks.
		if ($this->access_point == 'console') {
			$this->restoreRoute(['component' => ['console' => '']]);
		}

		if ($this->route_replaces) {
			if ($this->access_point == 'front') {
				$this->restoreRoute($this->route_replaces);
			} else {
				$this->route_replaces = [];
			}
		}

		// fullcomponent
		$this->fullcomponent = $this->component;
		if ($this->deepcomponent) {
			$this->fullcomponent .= '.' . $this->deepcomponent;
		}
	}

	/**
	 * Restore route
	 * 
	 * @param array $replaces
	 * 
	 * @return void
	 */
	protected function restoreRoute(array $replaces): void
	{
		do {
			if (in_array($this->component, $replaces['component'])) {
				$this->component = array_search($this->component, $replaces['component']);
			}

			if (
				$this->task
				&& isset($replaces['task'][$this->component])
				&& in_array($this->task, $replaces['task'][$this->component])
			) {
				$this->task = array_search($this->task, $replaces['task'][$this->component]);
			}

			if (is_dir(SYSTEM_ABSPATH . 'app/' . $this->component)) {
				$flag = true;
			} elseif (!isset($flag)) { // this is used in case an empty component is used.
				if ($this->deepcomponent) {
					$this->component .= '.' . $this->deepcomponent;
					$this->deepcomponent = '';
				}
				if ($this->task) {
					$this->component .= '/' . $this->task;
				}
				$this->task			= $this->component;
				$this->component	= '';
				$flag = false;
			} else {
				$flag = true;
			}
		} while (!$flag);
	}

	/**
	 * Resolve controller
	 */
	protected function resolveController(): void
	{
		$callback = $this->routes
			->import($this->access_point, $this->component)
			?->getController($this->getRoute());

		// resolve
		switch (gettype($callback)) {
			case 'NULL':
				$this->controller = $this->resolveControllerFrom();
				break;

			case 'string':
				$this->controller = $this->resolveControllerFrom('', $callback);
				break;

			case 'array':
				$this->controller = $this->resolveControllerFrom($callback[0], $callback[1]);
				break;

			case 'object':
				$this->controller = $callback;
				break;
		}
	}

	/**
	 * Resolves the controller from names
	 * 
	 * @param string $className
	 * 
	 * @return array|false
	 */
	protected function resolveControllerFrom(string $className = '', string $classMethod = ''): array|false
	{
		if ($className) {
			$parts = explode(' ', preg_replace('/[A-Z]/', ' $0', lcfirst($className)));
			$total = count($parts);

			if ($total < 3 || $parts[$total - 1] != 'Controller') {
				return false;
			}
			$component = strtolower($parts[1]);
		} else {
			$roller = [$this->access_point, $this->component];
			if ($this->deepcomponent) {
				$roller = array_merge($roller, explode('.', $this->deepcomponent));
			}
			$className = implode(array_map('ucfirst', $roller)) . 'Controller';
			$component = $this->component;
		}

		$file = SYSTEM_ABSPATH . 'app/' . $component . '/' . $className . '.php';

		// import
		is_file($file) and system_import($file);

		// method
		if (!$classMethod) {
			$classMethod = $this->task ?: 'index';
		}

		// snike_case to camelCase
		if (strpos($classMethod, '_') !== -1) {
			$classMethod = $this->snakeToCamelCase($classMethod);
		}

		// validate
		if (!method_exists($className, $classMethod)) {
			return false;
		}

		return [$className, $classMethod];
	}

	/**
	 * Snake to camel case
	 * 
	 * @param string $text
	 */
	protected function snakeToCamelCase(string &$text): string
	{
		return preg_replace_callback('/_(.)/', function ($match) {
			return strtoupper($match[1]);
		}, $text);
	}

	/**
	 * Initialize
	 */
	public function initialize(): void
	{
		// Initialize Url language.
		// This cannot be in the constructor because 
		// the Language constructor requires Router.
		$this->url_lang = app('language')->getUrlLang();

		// initialize
		$this->resolveRoute();
		$this->resolveController();
	}

	/**
	 * Get
	 */
	public function getArguments(callable $fn): void
	{
		$args = $this->routes->getArguments();

		if ($args) {
			$fn($args);
		}
	}

	/**
	 * Get
	 * 
	 * @return string
	 */
	public function getRoute(): string
	{
		$route = [];
		if ($this->access_point == 'front') {
			$route[] = '';
		} else {
			$route[] = $this->access_point;
		}
		$route[] = $this->fullcomponent;
		if ($this->task) {
			$route[] = $this->task;
		}
		return implode('/', $route);
	}

	/**
	 * Returns the current access_point.
	 * 
	 * @return string
	 */
	public function getAccessPoint(): string
	{
		return $this->access_point;
	}

	/**
	 * Return available access points.
	 * 
	 * @return array
	 */
	public function getAccessPoints(): array
	{
		return $this->access_points;
	}

	/**
	 * Returns the name of the current component.
	 * 
	 * @return string
	 */
	public function getComponent(bool $full = false): string
	{
		return $full ? $this->fullcomponent : $this->component;
	}

	/**
	 * Returns the current task.
	 * 
	 * @return string
	 */
	public function getTask(): string
	{
		return $this->task;
	}

	/**
	 * Returns the current format.
	 * 
	 * @return string
	 */
	public function getFormat(): string
	{
		return $this->format;
	}

	/**
	 * Is format
	 * 
	 * @return bool
	 */
	public function isFormat(string $format): bool
	{
		return $this->format == $format;
	}

	/**
	 * Returns a string, based on the currently running route.
	 * 
	 * @return string
	 */
	public function getHash(): string
	{
		return $this->component . ($this->deepcomponent ? '-' . $this->deepcomponent : '');
	}

	/**
	 * Returns the current controller.
	 * 
	 * @return Closure|array|false
	 */
	public function getController(): Closure|array|false
	{
		return $this->controller;
	}

	/**
	 * Returns the name of the current controller.
	 * 
	 * @return string
	 */
	public function getControllerAsString(): string
	{
		if (is_array($this->controller)) {
			return $this->controller[0] . '::' . $this->controller[1];
		} else {
			return var_export($this->controller, true);
		}
	}

	/**
	 * Get
	 */
	public function getMiddlewares(?RequestHandler $handler = null): void
	{
		$middlewares = config('router.middlewares')[$this->access_point] ?? null;

		if ($middlewares) {
			foreach ($middlewares as $middleware => $args) {
				$handler->add($middleware, ...$args);
			}
		}
	}

	/**
	 * Get Args With Format
	 * 
	 * @param string $format
	 * @param array  $args
	 * 
	 * @return array
	 */
	public function getArgsWithFormat(string $format, array $args = []): array
	{
		$args[$this->format_key] = $format;

		return $args;
	}

	/**
	 * Get a url
	 * 
	 * @param string $route
	 * @param array  $args
	 * @param bool   $absolute
	 * 
	 * @return string
	 */
	public function getUrl(string $route = '', array $args = [], bool $absolute = false): string
	{
		// The route is explicitly given by the extension.
		if (substr($route, 0, 1) == '#') {
			$route = $this->routes->getRouteFromId($route) ?? abort();
		}

		// inline args
		$replaces = [];
		if (preg_match_all('#/(?<holder>((?<prefix>[^/]+)=/)?\{(?<key>[^:]+?)(?:\:.+?)?\})#', $route, $matches, PREG_SET_ORDER)) {
			$clear = [];
			foreach ($matches as $match) {
				if (array_key_exists($match['key'], $args)) {
					if ($args[$match['key']]) {
						$replaces[$match['holder']] = (empty($match['prefix']) ? '' : $match['prefix'] . '/') . $args[$match['key']];
					}
					unset($args[$match['key']]);
				} else {
					$clear[] = $match[0];
				}
			}

			$route = str_replace($clear, '', $route);
		}

		// vars
		$route			= explode('/', $route, 3);
		$access_point	= $route[0];
		$component		= $route[1] ?? '';
		$task			= $route[2] ?? '';

		// replace rules
		if ($this->route_replaces) {
			if ($task && isset($this->route_replaces['task'][$component][$task])) {
				$task = $this->route_replaces['task'][$component][$task];
			}
			if (isset($this->route_replaces['component'][$component])) {
				$component = $this->route_replaces['component'][$component];
			}
		}

		if ($this->use_rewrite) {
			$route = [];

			// URL language mode
			if ($this->url_lang) {
				$route[] = $this->url_lang['value'];
			}
			if ($access_point) {
				$route[] = $access_point;
			}
			if ($component) {
				$route[] = $component;
			}
			if ($task) {
				$route[] = $task;
			}

			$url = implode('/', $route);

			if ($replaces) {
				$url = strtr($url, $replaces);
			}
		} else {
			// URL language mode
			if ($this->url_lang) {
				$args = [$this->url_lang['key'] => $this->url_lang['value']] + $args;
			}

			$url	= 'index.php';
			$goto	= [];

			if ($access_point) {
				$goto[] = $access_point;
			}
			if ($component) {
				$goto[] = $component;
			}
			if ($task) {
				$goto[] = $task;
			}

			if ($goto) {
				$goto = implode('/', $goto);

				if ($replaces) {
					$goto = strtr($goto, $replaces);
				}
				$args = [$this->route_key => $goto] + $args;
			}
		}

		if ($args) {
			$url .= '?' . $this->toQueryString($args);
		}

		return ($absolute ? $this->site_url : $this->site_baseurl) . $url;
	}

	/**
	 * Get a url
	 * 
	 * @param string $route
	 * @param array  $args
	 * @param bool   $absolute
	 * 
	 * @return string
	 */
	public function getCurrentUrl(bool $absolute = false): string
	{
		$queryParams = $this->queryParams;

		if ($this->use_rewrite) {
			$url = $this->getRoute();
		} else {
			$url = 'index.php';
			$queryParams[$this->route_key] = $this->getRoute();
		}

		if ($queryParams) {
			$url .= '?' . $this->toQueryString($queryParams);
		}

		return ($absolute ? $this->site_url : $this->site_baseurl) . $url;
	}

	/**
	 * Get a url for a form
	 * 
	 * Returns an array with a key corresponding to the action of the form,
	 * and another with the hidden elements that complete the arguments.
	 *
	 * @param string $route
	 * @param array  $args
	 * @param bool   $absolute
	 * 
	 * @return array
	 */
	public function getUrlForm($route = '', $args = [], $absolute = false): array
	{
		$url = [
			'action' => 'index.php',
			'hidden' => ''
		];

		if ($route) {
			$route			= explode('/', $route, 2);
			$access_point	= $route[0];
			$goto			= $route[1] ?? '';

			if ($access_point) {
				$url['action'] = $access_point . '/' . $url['action'];
			}
			if ($goto) {
				$url['hidden'] .= '<input type="hidden" name="' . $this->route_key . '" value="' . $goto . '"/>';
			}
		}

		if ($this->url_lang) {
			$url['hidden'] .= '<input type="hidden" name="' . $this->url_lang['key'] . '" value="' . $this->url_lang['value'] . '"/>';
		}

		if ($args) {
			foreach ($args as $key => $value) {
				$url['hidden'] .= '<input type="hidden" name="' . $key . '" value="' . $value . '"/>';
			}
		}
		$url['action'] = ($absolute ? $this->site_url : $this->site_baseurl) . $url['action'];

		return $url;
	}

	/**
	 * Redirect
	 * 
	 * @param string|array $url
	 * @param bool         $absolute
	 */
	public function redirect($url = null, bool $absolute = true)
	{
		if (!$url) {
			$url = $this->getUrl('', [], $absolute);
		} elseif ($url == -1) {
			$url = $_SERVER['HTTP_REFERER'] ?? '';
			if (substr($url, 0, strlen($this->site_url)) !== $this->site_url) {
				$url = $this->site_url;
			}
		} elseif ($url == 401) {
			$url = $this->getUrl('/usys/login', ['redirect' => urlencode($this->getCurrentUrl(true))], $absolute);
		} elseif ($url == 404) {
			$url = $this->getUrl('/system/404', [], $absolute);
		} elseif (is_array($url)) {
			$url[1] ??= [];
			$url[2] = $absolute;
			$url = call_user_func_array([$this, 'getUrl'], $url);
		} elseif (substr($url, 0, 4) !== 'http') {
			$url = ($absolute ? $this->site_url : $this->site_baseurl) . $url;
		}

		header("Location: $url");
		die;
	}

	/**
	 * Absolute Url
	 * 
	 * @param string $url
	 */
	public function absoluteUrl(string $url): string
	{
		if (substr($url, 0, $len = strlen($this->site_baseurl)) == $this->site_baseurl) {
			return $this->site_url . substr($url, $len);
		}

		return $url;
	}

	/**
	 * To query string
	 * 
	 * @param array $args
	 * 
	 * @return string
	 */
	protected function toQueryString(array $args): string
	{
		foreach ($args as $key => $value) {
			$args[$key] = $key . '=' . $value;
		}
		return implode('&', $args);
	}
}

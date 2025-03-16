<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Container;

use Closure;
use Psr\Container\ContainerInterface;
use Junco\Container\Exception\ContainerException;
use Junco\Container\Exception\NotFoundException;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Container
 */
class Container implements ContainerInterface
{
	// vars
	static protected $instance;
	//
	protected array $instances	= [];
	protected array $registers	= [];

	/**
	 * Singleton
	 */
	public static function getInstance(): self
	{
		if (self::$instance === null) {
			self::$instance = new self();
			self::$instance->initialize();
		}

		return self::$instance;
	}

	/**
	 * Initialize
	 */
	public function initialize()
	{
		$config = $this->get('config')->get('container');

		if ($config['container.system_registers']) {
			$this->registers = $config['container.system_registers'];
		}

		if ($config['container.user_registers']) {
			$this->registers = array_merge($this->registers, $config['container.user_registers']);
		}
	}

	/**
	 * Finds an entry of the container by its identifier and returns it.
	 *
	 * @param string $id Identifier of the entry to look for.
	 *
	 * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
	 * @throws ContainerExceptionInterface Error while retrieving the entry.
	 *
	 * @return mixed Entry.
	 */
	public function get(string $id)
	{
		if (isset($this->instances[$id])) {
			return $this->instances[$id];
		}

		if (isset($this->registers[$id])) {
			if (!$this->registers[$id]['shared']) {
				return $this->newInstance($id);
			}
		} else {
			$this->registerClassNameFromId($id);
		}

		return $this->instances[$id] = $this->newInstance($id);
	}

	/**
	 * Returns true if the container can return an entry for the given identifier.
	 *
	 * @param string $id Identifier of the entry to look for.
	 *
	 * @return bool
	 */
	public function has(string $id): bool
	{
		return isset($this->instances[$id]) || isset($this->registers[$id]);
	}

	/**
	 * Set
	 *
	 * @param string $id
	 * @param object $class
	 *
	 * @return void
	 */
	public function set(string $id, object $class, bool $rewrite = false): void
	{
		if (isset($this->instances[$id]) && !$rewrite) {
			throw new ContainerException(sprintf('An object with the id «%s» has already been registered', $id));
		}

		$this->instances[$id] = $class;
	}

	/**
	 * Unset an instance value from an id.
	 *
	 * @param string	$id
	 * 
	 * @return void
	 */
	public function unset(string $id): void
	{
		if (isset($this->instances[$id])) {
			unset($this->instances[$id]);
		}
	}

	/**
	 * Set
	 *
	 * @param string $id
	 * @param string $value
	 *
	 * @return void
	 */
	public function register(string $id, string $className = '', bool $shared = false): void
	{
		$this->registers[$id] = [
			'className' => $className ?: $id,
			'shared' => $shared
		];
	}

	/**
	 * New instance
	 *
	 * @param string $className
	 *
	 * @return object
	 */
	protected function newInstance(string $id): object
	{
		$className = $this->getRealClassName($this->registers[$id]['className']);

		// singleton
		if (isset($this->instances[$className])) {
			return $this->instances[$className];
		}

		if (!class_exists($className)) {
			throw new NotFoundException(sprintf('Error trying to instantiate class «%s»', $className));
		}

		// create new instance
		$reflector = new ReflectionClass($className);
		$constructor = $reflector->getConstructor();

		if ($constructor === null) {
			return new $className;
		}

		return $reflector->newInstanceArgs($this->getArguments($constructor));
	}

	/**
	 * Call
	 * 
	 * @param callable $callable
	 * 
	 * @return mixed
	 */
	public function call(callable $callable): mixed
	{
		return $this->isFunction($callable)
			? $this->callFunction($callable)
			: $this->callClass($callable);
	}

	/**
	 * Returns true if the parameter is a function.
	 * 
	 * @param mixed $callable
	 * 
	 * @return bool
	 */
	protected function isFunction(mixed $callable): bool
	{
		return (is_string($callable) && function_exists($callable))
			|| (is_object($callable) && ($callable instanceof Closure));
	}

	/**
	 * Call
	 */
	protected function callFunction(Closure|string $callable): mixed
	{
		$reflector = new ReflectionFunction($callable);

		if (!$reflector->getNumberOfRequiredParameters()) {
			return $reflector->invoke();
		}

		return $reflector->invokeArgs($this->getArguments($reflector));
	}

	/**
	 * Call
	 * 
	 * @param callable $callable
	 * 
	 * @return mixed
	 */
	protected function callClass(callable $callable): mixed
	{
		if (is_string($callable)) {
			$callable = explode('::', $callable, 2);
			$refClass = $callable[0];
			$method   = $callable[1];
			$class    = null;
		} else {
			$refClass = $callable[0];
			$method   = $callable[1];
			$class    = is_object($callable[0])
				? $callable[0]
				: null;
		}

		$classReflector = new ReflectionClass($refClass);
		$methodReflector = $classReflector->getMethod($method);

		if (!$methodReflector->getNumberOfRequiredParameters()) {
			return $methodReflector->invoke($class);
		}

		return $methodReflector->invokeArgs($class, $this->getArguments($methodReflector));
	}

	/**
	 * Gets the arguments
	 * 
	 * @param ReflectionMethod|ReflectionFunction $reflector
	 * 
	 * @throws ContainerException
	 * 
	 * @return array
	 */
	protected function getArguments(ReflectionMethod|ReflectionFunction $reflector): array
	{
		$args = [];

		foreach ($reflector->getParameters() as $parameter) {
			if ($parameter->isOptional()) {
				break;
			} elseif ($parameter->hasType()) {
				$args[] = $this->get($parameter->getType());
			} else {
				throw new ContainerException(sprintf('Error trying to instantiate «%s»', $reflector->getName()));
			}
		}

		return $args;
	}

	/**
	 * Set
	 *
	 * @param string $id
	 * @param array  $path
	 *
	 * @return string
	 */
	protected function getRealClassName(string $id, array $path = []): string
	{
		if (in_array($id, $path)) {
			throw new ContainerException(sprintf('Recursive error when instantiating the class «%s»', array_pop($path)));
		}

		if (
			isset($this->registers[$id])
			&& $this->registers[$id]['className'] !== $id
		) {
			$path[] = $id;
			return $this->getRealClassName($this->registers[$id]['className'], $path);
		}

		return $id;
	}

	/**
	 * Register class name from id.
	 *
	 * @param string $id
	 * 
	 * @return string
	 */
	protected function registerClassNameFromId(string $id): string
	{
		$className = $id;

		if (strpos($className, '\\') === false) {
			$className = ucfirst($className);
		}

		$this->registers[$id] = [
			'className' => $className,
			'shared' => true
		];

		return $className;
	}
}

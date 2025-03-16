<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Config
{
	// vars
	protected ?Settings $settings	= null;
	protected array     $data		= [];

	/**
	 * Constructor
	 * 
	 * @param Settings $settings
	 */
	public function __construct(?Settings $settings = null)
	{
		$this->settings = $settings ?? new Settings();
	}

	/**
	 * Has
	 * 
	 * @param string $key
	 * 
	 * @return bool
	 */
	public function has(string $key = ''): bool
	{
		if (!$key) {
			return false;
		}

		$this->explode($key, $scope, $name);
		$this->data[$scope] ??= $this->loadConfig($scope, false);

		return $name
			? isset($this->data[$scope][$key])
			: (bool)$this->data[$scope];
	}

	/**
	 * Get
	 * 
	 * @param string $key
	 * 
	 * @return mixed
	 */
	public function get(string $key = ''): mixed
	{
		if (!$key) {
			return $this->data;
		}

		$this->explode($key, $scope, $name);
		$this->data[$scope] ??= $this->loadConfig($scope);

		return $name
			? ($this->data[$scope][$key] ?? null)
			: $this->data[$scope];
	}

	/**
	 * Set
	 * 
	 * @param string $key
	 * @param mixed  $value
	 * 
	 * @return bool
	 */
	public function set(string $key = '', mixed $value = null): bool
	{
		$this->explode($key, $scope, $name);

		if (!$scope) {
			throw new Error('The configuration scope is empty.');
		}

		$this->data[$scope] ?? $this->get($scope);

		if ($name) {
			$this->data[$scope][$key] = $value;
		} else {
			if (!is_array($value)) {
				return false;
			}

			$this->data[$scope] = array_merge($this->data[$scope], $value);
		}

		return true;
	}

	/**
	 * Explode
	 * 
	 * @param string  $key
	 * @param ?string &$scope
	 * @param ?string &$name
	 *  
	 * @return void
	 */
	protected function explode(string $key, ?string &$scope = null, ?string &$name = null): void
	{
		$part  = explode('.', $key, 2);
		$scope = $part[0];
		$name  = $part[1] ?? null;
	}

	/**
	 * Load
	 * 
	 * @param string $scope
	 *  
	 * @return array
	 */
	protected function loadConfig(string $scope, bool $force = true): array
	{
		$data = $this->settings
			->setKey($scope)
			->get($force);

		return $data
			? $this->setPrefix($scope, $data)
			: [];
	}

	/**
	 * Returns an array with the variable name preceded by the scope.
	 * 
	 * @param string $scope
	 * @param array  $data
	 * 
	 * @return array
	 */
	protected function setPrefix(string $scope, array $data): array
	{
		$newData = [];
		foreach ($data as $key => $value) {
			$newData[$scope . '.' . $key] = $value;
		}

		return $newData;
	}
}

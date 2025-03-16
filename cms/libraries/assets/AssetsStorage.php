<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class AssetsStorage extends AssetsBasic
{
	/**
	 * Store
	 * 
	 * @param string $newKey
	 * @param array  $data
	 * @param string $curKey
	 * 
	 * @throws Exception
	 * 
	 * @return void
	 */
	public function store(string $newKey, array $data, string $curKey = ''): void
	{
		$isCreated  = !$curKey;
		$keyChanged = $curKey && $curKey !== $newKey;

		// validate
		if (($isCreated || $keyChanged) && $this->fetch($newKey)) {
			throw new Exception(_t('The key already exists.'));
		}

		if (
			($data['default_assets'] !== $data['assets'])
			&& $this->isDev($newKey)
		) {
			if ($this->hasResourcesReplaced($data['assets'])) {
				throw new Exception(_t('The developer can not replace the resources.'));
			}

			$data['default_assets'] = $data['assets'];
		}

		// store
		$this->storeData($newKey, $data);

		// delete old key
		$keyChanged and $this->delete($curKey);

		$this->compile(
			$newKey,
			$data['assets'],
			(bool)$this->config['assets.minify'],
			(int)$this->config['assets.fixurl'],
			(int)$this->config['assets.precompile']
		);

		$this->updateVersion();
	}

	/**
	 * Remove
	 *
	 * @param string $key
	 *
	 * @return void
	 */
	public function delete(string $key): void
	{
		$a = $this->getDataFile($key);
		$b = $this->getMinifyFile($key);

		is_file($a) and unlink($a);
		is_file($b) and unlink($b);
	}

	/**
	 * Remove
	 *
	 * @param array $keys
	 *
	 * @return void
	 */
	public function removeAll(array $keys): void
	{
		foreach ($keys as $key) {
			$this->delete($key);
		}
	}

	/**
	 * Remove all from aliasis
	 *
	 * @param array $aliases
	 *
	 * @return void
	 */
	public function removeAllFromAliases(array $aliases): void
	{
		if (!$aliases) {
			return;
		}

		foreach ($this->getAllKeysFromAliases($aliases) as $key) {
			$this->delete($key);
		}
	}

	/**
	 * Is developder
	 * 
	 * @param string $key
	 * 
	 * @return bool
	 */
	protected function isDev(string $key): bool
	{
		return SYSTEM_DEVELOPER_MODE && db()->safeFind("
		SELECT
		 COUNT(*)
		FROM `#__extensions` e
		LEFT JOIN `#__extensions_developers` d ON ( e.developer_id = d.id )
		WHERE e.extension_alias = ?
		AND d.is_protected = 0", $this->getExtensionAliasFromKey($key))->fetchColumn();
	}

	/**
	 * Has
	 * 
	 * @param string $sheet
	 * 
	 * @return bool
	 */
	protected function hasResourcesReplaced(string $sheet): bool
	{
		return false !== strstr($this->clear($sheet), ':');
	}
}

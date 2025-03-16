<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class AssetsExplainer extends AssetsBasic
{
	// vars
	protected $assets = [];

	/**
	 * Obtains the source assets from the compiled assets.
	 * 
	 * @param array $assets   An array of all assets to be explained.
	 * 
	 * @return array
	 */
	public function explain(array $assets): array
	{
		$this->assets = [];

		foreach ($assets as $asset) {
			$asset and $this->explainAsset($asset);
		}

		return $this->assets;
	}

	/**
	 * Explain file
	 * 
	 * @param array|string $asset
	 * 
	 * @return void
	 */
	protected function explainAsset(array|string $asset): void
	{
		if ($this->itWasExplained($asset['href'] ?? $asset['src'] ?? $asset)) {
			return;
		}

		if (is_array($asset)) {
			$this->assets[] = $asset;
		} elseif (pathinfo($asset, PATHINFO_EXTENSION) == 'css') {
			$this->assets[] = ['rel' => 'stylesheet', 'type' => 'text/css', 'href' => $asset];
		} else {
			$this->assets[] = ['src' => $asset];
		}
	}

	/**
	 * Try to include the list of original files.
	 * 
	 * @param string $file
	 * 
	 * @return false
	 */
	protected function itWasExplained(string $file): bool
	{
		if (preg_match('#^assets/([\w-]+)\.min\.(css|js)$#', $file, $match)) {
			$data = $this->fetch($match[1] . '.' . $match[2]);

			if ($data) {
				foreach ($this->explodeAssets($data['assets']) as $asset) {
					$this->explainAsset($asset[0]);
				}

				return true;
			}
		}

		return false;
	}
}

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class SettingsExporter extends Settings
{
	/**
	 * Exports the settings to a given destination.
	 * 
	 * @param string $dirpath  The path to the destination.
	 * @param bool   $setUp    Create the file with the values.
	 * 
	 * @return int   The number of files imported.
	 */
	public function export(string $dirpath, bool $setUp = false): int
	{
		$rows = $this->getAllData();

		if (!$rows) {
			return 0;
		}

		$settings = new Settings($this->key, $dirpath, true);

		foreach ($rows as $key => $data) {
			$settings->setKey($key);

			if ($setUp) {
				$settings->set($data);
			}

			$settings->save($data);
		}

		return count($rows);
	}
}

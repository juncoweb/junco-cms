<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Settings;

use Settings;

class Exporter extends Settings
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

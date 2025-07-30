<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class BackendModel extends Model
{
    /**
     * Get
     */
    public function getMenusData()
    {
        // cache
        $allow_cache = SYSTEM_ALLOW_CACHE;
        $json        = null;

        if ($allow_cache) {
            $cache_key    = 'backend-search-menus#';
            $cache        = cache();
            $json         = $cache->get($cache_key);
        }

        if (!$json) {
            $json = $this->getAll();
            $allow_cache and $cache->set($cache_key, $json);
        }

        return $json;
    }

    /**
     * Get
     */
    protected function getAll(): array
    {
        $keys = [
            'backend-Default' => ['type' => 0],
            'settings-Default' => ['type' => 1],
        ];

        // query
        $rows = db()->query("
		SELECT
		 menu_key ,
		 menu_url ,
		 menu_path
		FROM `#__menus`
		WHERE menu_key IN (?..)
		AND menu_url NOT IN ('', 'javascript:void(0)')
		ORDER BY menu_key, menu_path", array_keys($keys))->fetchAll();

        $rows  = (new Menus)->parse($rows);
        $urls  = [];
        $menus = [];

        foreach ($rows as $row) {
            if (in_array($row['menu_url'], $urls)) {
                continue;
            }

            $urls[] = $row['menu_url'];
            $menus[] = [
                $keys[$row['menu_key']]['type'],
                $row['menu_name'],
                $row['menu_url']
            ];
        }

        return $menus;
    }
}

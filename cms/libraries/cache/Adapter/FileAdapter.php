<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Cache\Adapter;

class FileAdapter implements AdapterInterface
{
    // vars
    protected string $dirpath = SYSTEM_STORAGE . 'cache/';

    /**
     * Fetches a value from the cache.
     *
     * @param string $key      The unique key of this item in the cache.
     *
     * @return mixed The value of the item from the cache, or null in case of cache miss.
     */
    public function get(string $key): mixed
    {
        $file = $this->dirpath . $key;

        if (is_file($file)) {
            $data = file_get_contents($file);

            if ($data !== false) {
                $data = unserialize($data);

                if (!($data[0] && $data[0] < time())) {
                    return $data[1];
                }
            }
        }

        return null;
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string    $key     The key of the item to store.
     * @param mixed     $value   The value of the item to store, must be serializable.
     * @param ?int		$ttl     The timestamp to expires.
     *
     * @return bool True on success and false on failure.
     */
    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        if ($ttl) {
            $ttl += time();
        }
        $file = $this->dirpath . $key;
        $dir  = dirname($file);
        $data = serialize([$ttl ?: 0, $value]);

        is_dir($dir) or mkdir($dir, SYSTEM_MKDIR_MODE, true);

        return false !== file_put_contents($file, $data);
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     */
    public function delete(string $key): bool
    {
        $file = $this->dirpath . $key;

        return is_file($file) and unlink($file);
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear(string $pool = ''): bool
    {
        $dir = $this->dirpath . $pool . '/';
        $cdir = is_dir($dir) ? scandir($dir) : false;

        if (false === $cdir) {
            return false;
        }

        $cdir = array_diff($cdir, ['.', '..']);
        foreach ($cdir as $has) {
            if (
                is_file($dir . $has)
                && !unlink($dir . $has)
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * @param string $key The cache item key.
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return is_file($this->dirpath . $key);
    }

    /**
     * Get keys
     * 
     * @return array
     */
    public function getKeys(): array
    {
        $keys = [];
        $cdir = is_dir($this->dirpath) ? scandir($this->dirpath) : false;

        if (false !== $cdir) {
            foreach ($cdir as $has) {
                if (
                    $has != '.'
                    && $has != '..'
                    && $has != '.htaccess'
                    && is_file($this->dirpath . $has)
                ) {
                    $keys[] = $has;
                }
            }
        }

        return $keys;
    }
}

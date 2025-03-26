<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Cache\Adapter;

class NullAdapter implements AdapterInterface
{
    /**
     * Fetches a value from the cache.
     *
     * @param string $key      The unique key of this item in the cache.
     *
     * @return mixed The value of the item from the cache, or null in case of cache miss.
     */
    public function get(string $key): mixed
    {
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
        return false;
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
        return false;
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear(string $pool = ''): bool
    {
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
        return false;
    }

    /**
     * Get keys
     * 
     * @return array
     */
    public function getKeys(): array
    {
        return [];
    }
}

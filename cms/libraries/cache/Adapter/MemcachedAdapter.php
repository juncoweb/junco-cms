<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Cache\Adapter;

use Memcached;

class MemcachedAdapter implements AdapterInterface
{
    // vars
    protected object $memcached;
    protected string $prefix = 'cache.';

    /**
     * Constructor
     */
    public function __construct()
    {
        $config = config('cache-memcached');

        $this->memcached = new Memcached($config['cache-memcached.id'] ?: null);
        $result = $this->connect($config['cache-memcached.host'], $config['cache-memcached.port']);

        if (!$result) {
            throw new \Exception('Memcached failed to connect correctly');
        }
    }

    /**
     * Fetches a value from the cache.
     *
     * @param  string $key	The unique key of this item in the cache.
     * 
     * @return mixed The value of the item from the cache, or null in case of cache miss.
     */
    public function get(string $key): mixed
    {
        $result = $this->memcached->get($this->prefix . $key);

        return $result !== false ? $result : null;
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
        return $this->memcached->set($this->prefix . $key, $value, $ttl ?: 0);
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
        return $this->memcached->delete($this->prefix . $key);
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear(string $pool = ''): bool
    {
        return $this->memcached->flush();
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
        return $this->memcached->get($this->prefix . $key) !== false;
    }

    /**
     * Return the keys of all stored data.
     *
     * @return array
     */
    public function getKeys(): array
    {
        $keys = [];
        $rows = $this->memcached->getAllKeys();

        if ($rows) {
            $prefix = explode('.', $this->prefix, 2)[0];

            foreach ($rows as $key) {
                $part = explode('.', $key, 2);

                if ($part[0] == $prefix) {
                    $keys[] = $part[1] ?? '';
                }
            }
        }

        return $keys;
    }

    /**
     * Conect
     * 
     * @param string $host
     * @param int    $port
     * 
     * @return bool
     */
    protected function connect(string $host, int $port): bool
    {
        $servers = $this->memcached->getServerList();

        if ($servers) {
            foreach ($servers as $server) {
                if ($server['host'] == $host and $server['port'] == $port) {
                    return true;
                }
            }
        }

        return $this->memcached->addServer($host, $port);
    }
}

<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Cache\Adapter;

use \Redis;

class RedisAdapter implements AdapterInterface
{
    // vars
    protected object $redis;
    protected string $prefix = 'cache.';

    /**
     * Constructor
     */
    public function __construct()
    {
        $config = config('cache-redis');

        $this->redis = new Redis();
        $this->redis->pconnect($config['cache-redis.host'], $config['cache-redis.port']);
        //$this->redis->auth($config['cache-redis.password']);
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
        $data = $this->redis->get($this->prefix . $key);

        return json_decode($data, true);
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
        //return $this->redis->setEx($this->prefix . $key, $ttl ?: 0, json_encode($value));

        $result = $this->redis->set($this->prefix . $key, json_encode($value));

        if ($result) {
            return $this->redis->expire($this->prefix . $key, $ttl ?: 0);
        }
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
        return $this->redis->del($this->prefix . $key);
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear(string $pool = ''): bool
    {
        $this->redis->flushdb();
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
        return $this->redis->exists($this->prefix . $key);
    }

    /**
     * Return the keys of all stored data.
     *
     * @return array
     */
    public function getKeys(): array
    {
        $keys = [];

        // Without enabling Redis::SCAN_RETRY (default condition)
        $it = NULL;
        do {
            $arr_keys = $this->redis->scan($it);

            // Redis may return empty results, so protect against that
            if ($arr_keys !== false) {
                foreach ($arr_keys as $key) {
                    $keys[] = $key;
                }
            }
        } while ($it > 0);

        // With Redis::SCAN_RETRY enabled
        $this->redis->setOption(\Redis::OPT_SCAN, \Redis::SCAN_RETRY);
        $it = NULL;

        // phpredis will retry the SCAN command if empty results are returned from the
        // server, so no empty results check is required.
        while ($arr_keys = $this->redis->scan($it)) {
            foreach ($arr_keys as $key) {
                $keys[] = $key;
            }
        }

        return $keys;
    }
}

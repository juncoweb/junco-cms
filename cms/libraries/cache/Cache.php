<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Psr\SimpleCache\CacheInterface;
use Junco\Cache\Adapter\AdapterInterface;

class Cache implements CacheInterface
{
    // vars
    protected AdapterInterface $adapter;
    protected string $lang;

    /**
     * Constructor
     */
    public function __construct(?AdapterInterface $adapter = null)
    {
        $this->adapter = $adapter ?: $this->getAdapter();
        $this->lang = app('language')->getCurrent();
    }

    /**
     * Fetches a value from the cache.
     *
     * @param  string $key      The unique key of this item in the cache.
     * @param  mixed  $default  Default value to return if the key does not exist.
     * @return mixed            The value of the item from the cache, or $default in case of cache miss.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $key = $this->validateKey($key);
        $value = $this->adapter->get($key);

        if ($value === null) {
            return $default;
        }

        return $value;
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string                 $key   The key of the item to store.
     * @param mixed                  $value The value of the item to store, must be serializable.
     * @param null|int|DateInterval  $ttl   The TTL value of this item.
     *
     * @return bool True on success and false on failure.
     */
    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        $key = $this->validateKey($key);

        if ($ttl instanceof DateInterval) {
            $ttl = $this->DateIntervalToInt($ttl);
        }

        return false !== $this->adapter->set($key, $value, $ttl);
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
        return $this->adapter->delete(
            $this->validateKey($key)
        );
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear(): bool
    {
        return $this->adapter->clear();
    }

    /**
     * Wipes clean the entire cache's keys.
     * 
     * @param $pool
     *
     * @return bool True on success and false on failure.
     */
    public function clearPool(string $pool): bool
    {
        if (preg_match('/[^\w]/', $pool)) {
            throw new Error('The cache pool is invalid');
        }

        return $this->adapter->clear($pool);
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys    A list of keys that can obtained in a single operation.
     * @param mixed    $default Default value to return for keys that do not exist.
     *
     * @return iterable A list of key => value pairs.
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $result = [];
        foreach ($keys as $key) {
            $value = $this->get($key);
            $result[$key] = $value !== null ? $value : $default;
        }

        return $result;
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable               $values A list of key => value pairs for a multiple-set operation.
     * @param null|int|DateInterval  $ttl    Optional. The TTL value of this item.
     * 
     * @return bool True on success and false on failure.
     */
    public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool
    {
        if ($ttl instanceof DateInterval) {
            $ttl = $this->DateIntervalToInt($ttl);
        }
        foreach ($values as $key => $value) {
            $key = $this->validateKey($key);

            if (false === $this->adapter->set($key, $value, $ttl)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys A list of string-based keys to be deleted.
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     */
    public function deleteMultiple($keys): bool
    {
        foreach ($keys as $key) {
            if (!$this->delete($key)) {
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
    public function has($key): bool
    {
        return $this->adapter->has($key);
    }

    /**
     * Return the keys of all stored data.
     *
     * @return array
     */
    public function getKeys(): array
    {
        return $this->adapter->getKeys();
    }

    /**
     * Get
     */
    protected function getAdapter()
    {
        switch (config('cache.adapter')) {
            case 'apcu':
                return new Junco\Cache\Adapter\ApcuAdapter();

            case 'null':
                return new Junco\Cache\Adapter\NullAdapter();

            case 'memcached':
                return new Junco\Cache\Adapter\MemcachedAdapter();

            case 'redis':
                return new Junco\Cache\Adapter\RedisAdapter();

            default:
            case 'file':
                return new Junco\Cache\Adapter\FileAdapter();
        }
    }

    /**
     * Get
     *
     * @param DateInterval $ttl   The TTL value of this item.
     * 
     * @return int
     */
    protected function DateIntervalToInt(DateInterval $ttl): int
    {
        return (new DateTime('now'))->add($ttl)->getTimeStamp();
    }

    /**
     * Validate
     *
     * @param string $key The cache item key.
     * 
     * @throws Error
     * 
     * @return string
     */
    protected function validateKey(string $key)
    {
        if (!preg_match('/^(?:(?<pool>\w){1,64}\/)?(?<key>[\w.-]+)(?<lang>\#)?$/i', $key, $match)) {
            throw new Error('The cache key is invalid');
        }
        if (isset($match['lang'])) {
            $key = str_replace('#', '.' . $this->lang, $key);
        }

        return $key;
    }
}

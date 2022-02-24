<?php

namespace Telanflow\Sms\Storage;

use Illuminate\Support\Facades\Cache;

/**
 * Class CacheStorage.
 */
class CacheStorage implements StorageInterface
{
    /**
     * @var int
     */
    protected static $lifetime = 120;

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        Cache::put($key, $value, self::$lifetime);
    }

    /**
     * @param $key
     * @param $default
     *
     * @return mixed
     */
    public function get($key, $default)
    {
        return Cache::get($key, $default);
    }

    /**
     * @param $key
     */
    public function forget($key)
    {
        if (Cache::has($key)) {
            Cache::forget($key);
        }
    }
}

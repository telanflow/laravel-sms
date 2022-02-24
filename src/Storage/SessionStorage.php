<?php

namespace Telanflow\Sms\Storage;

/**
 * Class SessionStorage.
 */
class SessionStorage implements StorageInterface
{
    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        session([
            $key => $value,
        ]);
    }

    /**
     * @param $key
     * @param $default
     *
     * @return mixed
     */
    public function get($key, $default)
    {
        return session($key, $default);
    }

    /**
     * @param $key
     */
    public function forget($key)
    {
        session()->forget($key);
    }
}

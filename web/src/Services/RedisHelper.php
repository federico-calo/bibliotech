<?php

namespace App\Services;

use App\Core\Settings;
use Redis;

class RedisHelper
{
    private Redis $redis;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect(Settings::get('redisHost'));
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param int    $ttl
     *
     * @return bool
     */
    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        return $this->redis->setex($key, $ttl, json_encode($value));
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key): mixed
    {
        $value = $this->redis->get($key);
        return $value ? json_decode((string) $value, true) : null;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function clearCache(string $key): bool
    {
        return $this->redis->del($key) > 0;
    }
    public function clearCacheAll(): bool
    {
        return $this->redis->flushDB();
    }

    /**
     * @param $pattern
     *
     * @return void
     */
    public function clearCacheFromPattern($pattern): void
    {
        $iterator = null;
        do {
            $keys = $this->redis->scan($iterator, $pattern);
            if ($keys) {
                foreach ($keys as $key) {
                    $this->redis->del($key);
                }
            }
        } while ($iterator != 0);
    }
}

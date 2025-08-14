<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class Idempotency
{
    /**
     * Intenta adquirir un lock por llave por N segundos.
     * Devuelve true si se adquirió, false si otro proceso ya lo tiene.
     */
    public static function lock(string $key, int $ttlSeconds = 600): bool
    {
        $redisKey = 'lock:' . $key;

        try {
            $resp = Redis::set($redisKey, '1', ['nx' => true, 'ex' => $ttlSeconds]);
            return $resp === true || $resp === 'OK';
        } catch (\Throwable $e) {
            // Tests o entornos sin Redis: usar caché
            return Cache::add($redisKey, 1, $ttlSeconds);
        }
    }
}

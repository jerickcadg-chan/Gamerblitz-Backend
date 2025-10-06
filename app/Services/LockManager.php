<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\Lock;

class LockManager
{
    /**
     * Acquire a lock for a given key.
     *
     * @param string $key
     * @param int $seconds
     * @return Lock|null Returns Lock instance if acquired, null if already held
     */
    public static function acquire(string $key, int $seconds = 240): ?Lock
    {
        $lock = Cache::lock($key, $seconds);
        return $lock->get() ? $lock : null;
    }

    /**
     * Release a lock.
     *
     * @param Lock $lock
     */
    public static function release(Lock $lock): void
    {
        $lock->release();
    }

    /**
     * Check if a lock is currently held.
     *
     * @param string $key
     * @param int $seconds
     * @return bool True if lock is held, false if free
     */
    public static function isRunning(string $key, int $seconds = 240): bool
    {
        $lock = Cache::lock($key, $seconds);
        if ($lock->get()) {
            // lock was free, release immediately
            $lock->release();
            return false;
        }
        return true; // lock is held
    }
}


<?php

namespace App\Listeners;

use App\Events\UserIpLogged;
use Illuminate\Support\Facades\DB;

class LogUserIp
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserIpLogged $event): void
    {
        // Check if the last log for this user and action has the same IP
        $lastLog = DB::table('user_ip_logs')
            ->where('user_id', $event->userId)
            ->where('action', $event->action)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastLog && $lastLog->ip_address === $event->ipAddress) {
            // Skip logging if IP hasn't changed
            return;
        }

        DB::table('user_ip_logs')->insert([
            'user_id' => $event->userId,
            'ip_address' => $event->ipAddress,
            'action' => $event->action,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Keep only the latest 10 IP logs per user
        $count = DB::table('user_ip_logs')->where('user_id', $event->userId)->count();
        if ($count > 10) {
            $toDelete = $count - 10;
            DB::table('user_ip_logs')
                ->where('user_id', $event->userId)
                ->orderBy('created_at', 'asc')
                ->limit($toDelete)
                ->delete();
        }
    }
}

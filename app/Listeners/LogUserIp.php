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
         // Always log the user action with IP for complete audit trail
         DB::table('user_ip_logs')->insert([
             'user_id' => $event->userId,
             'ip_address' => $event->ipAddress,
             'action' => $event->action,
             'created_at' => now(),
             'updated_at' => now(),
         ]);

          // Keep only the latest 10 IP logs per user or per IP for guests to maintain storage efficiency
          if ($event->userId) {
              $count = DB::table('user_ip_logs')->where('user_id', $event->userId)->count();
              if ($count > 10) {
                  $toDelete = $count - 10;
                  DB::table('user_ip_logs')
                      ->where('user_id', $event->userId)
                      ->orderBy('created_at', 'asc')
                      ->limit($toDelete)
                      ->delete();
              }
          } else {
              $count = DB::table('user_ip_logs')->whereNull('user_id')->where('ip_address', $event->ipAddress)->count();
              if ($count > 10) {
                  $toDelete = $count - 10;
                  DB::table('user_ip_logs')
                      ->whereNull('user_id')
                      ->where('ip_address', $event->ipAddress)
                      ->orderBy('created_at', 'asc')
                      ->limit($toDelete)
                      ->delete();
              }
          }
     }
}

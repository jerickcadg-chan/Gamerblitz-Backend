<?php

namespace App\Listeners;

use App\Events\UserActivityLogged;
use Illuminate\Support\Facades\DB;

class LogUserActivity
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
     public function handle(UserActivityLogged $event): void
     {
         // Always log the user action with IP for complete audit trail
          DB::table('user_activity_logs')->insert([
             'user_id' => $event->userId,
             'ip_address' => $event->ipAddress,
             'action' => $event->action,
             'created_at' => now(),
             'updated_at' => now(),
         ]);

           // Keep only the latest 100 IP logs per user or per IP for guests to maintain storage efficiency
           if ($event->userId) {
                $count = DB::table('user_activity_logs')->where('user_id', $event->userId)->count();
               if ($count > 100) {
                   $toDelete = $count - 100;
                   DB::table('user_activity_logs')
                       ->where('user_id', $event->userId)
                      ->orderBy('created_at', 'asc')
                      ->limit($toDelete)
                      ->delete();
              }
           } else {
                $count = DB::table('user_activity_logs')->whereNull('user_id')->where('ip_address', $event->ipAddress)->count();
               if ($count > 100) {
                   $toDelete = $count - 100;
                   DB::table('user_activity_logs')
                       ->whereNull('user_id')
                       ->where('ip_address', $event->ipAddress)
                      ->orderBy('created_at', 'asc')
                      ->limit($toDelete)
                      ->delete();
              }
          }
     }
}

<?php

namespace App\Listeners;

use App\Events\UserLoginHistory;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\UserLoginHistory as ModelsUserLoginHistory;

class StoreloginHistory
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UserLoginHistory  $event
     * @return void
     */
    public function handle(UserLoginHistory $event)
    {
        $loginTime = Carbon::now()->toDateTimeString();
        $userDetails = $event->user;

        $input = [
            'name' => $userDetails->name,
            'email' => $userDetails->email,
            'username' => $userDetails->username,
            'login_time' => $loginTime,
            'ip' => $userDetails->ip,
        ];

        $saveHistory = ModelsUserLoginHistory::create($input);

        return $saveHistory;
    }
}

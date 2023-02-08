<?php

namespace App\Listener;

use App\Event\OrderCompleted;
use App\Models\User;
use App\Notifications\OrderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifySellers
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
     * @param  \App\Event\OrderCompleted  $event
     * @return void
     */
    public function handle(OrderCompleted $event)
    {
        $user = User::find(1);

        $user->notify(new OrderNotification($event->order));
    }
}

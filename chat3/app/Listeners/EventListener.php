<?php

namespace App\Listeners;

use App\Events\ChatMessageWasReceived;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventListener
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
     * @param  ChatMessageWasReceived  $event
     * @return void
     */
    public function handle(ChatMessageWasReceived $event)
    {
        //var_dump($event);
    }
}

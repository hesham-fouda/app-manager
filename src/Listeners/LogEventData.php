<?php

namespace AppManager\Listeners;

use AppManager\Events\ManagerEvent;
use Illuminate\Support\Facades\Log;

class LogEventData
{
    /**
     * Handle the event.
     *
     * @param  ManagerEvent  $event
     * @return void
     */
    public function handle(ManagerEvent $event)
    {
        if (config('app_manager.core.events.logEvents')) {
            Log::{$event->envelop['type']}($event->envelop['message'], $event->envelop['data']);
        }
    }
}

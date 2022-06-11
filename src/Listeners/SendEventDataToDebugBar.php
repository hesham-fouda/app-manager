<?php

namespace AppManager\Listeners;

use AppManager\Events\ManagerEvent;
use DebugBar\DataCollector\MessagesCollector;

class SendEventDataToDebugBar
{
    /**
     * @var MessagesCollector
     */
    protected $collector;

    public function __construct($collector = null)
    {
        if ($collector && $this->debugBarLoaded()) {
            app()->make('debugbar')->addCollector($this->collector = $collector);
        }
    }

    private function debugBarLoaded()
    {
        return app()->environment('local') && class_exists(MessagesCollector::class);
    }

    /**
     * Handle the event.
     *
     * @param ManagerEvent $event
     * @return void
     */
    public function handle(ManagerEvent $event)
    {
        if (!$this->debugBarLoaded())
            return;

        // todo: $event->envelop['data'] is not working here
        $this->collector->{$event->envelop['type']}($event->envelop['message']);
    }
}

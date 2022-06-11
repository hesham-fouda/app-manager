<?php

namespace AppManager\Events;

use Illuminate\Foundation\Events\Dispatchable;

class ManagerEvent
{
    use Dispatchable;

    /**
     * @var array
     */
    public $envelop;

    /**
     * Create a new event instance.
     *
     * @param array $envelop
     */
    public function __construct(array $envelop)
    {
        $this->envelop = $envelop;
    }
}

<?php

namespace Cog\Laravel\Love\Reactant\Listeners\Traits;

use Illuminate\Support\Facades\Config;

Trait OptionalQueueConnection
{
    public function __get($name)
    {
        if ($name == 'connection') {
            return Config::get('love.queue_connection', null);
        }
    }
}
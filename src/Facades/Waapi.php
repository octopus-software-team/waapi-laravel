<?php

namespace OctopusTeam\Waapi\Facades;

use Illuminate\Support\Facades\Facade;

class Waapi extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \OctopusTeam\Waapi\Waapi::class;
    }
}
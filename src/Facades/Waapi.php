<?php

namespace OctopusTeam\Waapi\Facades;

use Illuminate\Support\Facades\Facade;

class Waapi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \OctopusTeam\Waapi\Waapi::class;
    }
}
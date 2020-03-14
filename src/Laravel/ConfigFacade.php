<?php

namespace OpenVPN\Laravel;

use Illuminate\Support\Facades\Facade;

class ConfigFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ConfigWrapper::class;
    }
}

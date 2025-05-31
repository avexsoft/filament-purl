<?php

namespace Avexsoft\FilamentPurl\Facades;

use Illuminate\Support\Facades\Facade;

class FilamentPurl extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'filament-purl';
    }
}

<?php

namespace Koellich\LaravelPersources\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Koellich\LaravelPersources\LaravelPersources
 */
class LaravelPersources extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Koellich\LaravelPersources\LaravelPersources::class;
    }
}

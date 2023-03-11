<?php

namespace Koellich\Persources\Facades;

use Illuminate\Support\Facades\Facade;
use Koellich\Persources\Resource;

/**
 * @method static Resource|null getResourceFor(string $permission)
 * @method static array getResources()
 * @method static void registerResources()
 * @method static string getResourcesPath()
 * @method static string getAction(string $permission)
 *
 * @see \Koellich\Persources\Persources
 */
class Persources extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Koellich\Persources\Persources::class;
    }
}

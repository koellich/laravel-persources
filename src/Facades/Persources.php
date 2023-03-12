<?php

namespace Koellich\Persources\Facades;

use Illuminate\Support\Facades\Facade;
use Koellich\Persources\Resource;

/**
 * @method static Resource|null getResourceFor(string $permission)
 * @method static array getResources()
 * @method static void registerResources()
 * @method static string getResourcesPath()
 * @method static string getViewsPath()
 * @method static string getAction(string $permission)
 * @method static array getImpliedActions(string $action)
 * @method static string getRouteName(string $permission, string $impliedAction)
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

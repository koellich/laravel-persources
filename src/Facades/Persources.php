<?php

namespace Koellich\Persources\Facades;

use Illuminate\Support\Facades\Facade;
use Koellich\Persources\Resource;

/**
 * @method static bool checkPermission(string $permission)
 * @method static Resource|null getResourceForPermission(string $permission)
 * @method static Resource|null getResourceForModel(string $model)
 * @method static array getResources()
 * @method static void registerResources()
 * @method static string getResourcesPath()
 * @method static string getViewsPath()
 * @method static string getAction(string $permission)
 * @method static string getHttpMethod(string $action)
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

<?php

namespace Koellich\Persources\Facades;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed list(Request $request)
 * @method static mixed view(Request $request, $id)
 * @method static mixed create(Request $request)
 * @method static mixed update(Request $request, $id)
 * @method static mixed delete(Request $request, $id)
 * @method static array getPermissions()
 *
 * @see \Koellich\Persources\Resource
 */
class Resource extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Koellich\Persources\Resource::class;
    }
}

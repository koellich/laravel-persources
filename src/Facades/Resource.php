<?php

namespace Koellich\Persources\Facades;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed list(Request $request)
 * @method static mixed view(Request $request, $id)
 * @method static mixed create(Request $request)
 * @method static mixed update(Request $request, $id)
 * @method static mixed delete($id)
 * @method static string getModelClassName()
 * @method static array getPermissions()
 * @method static int getItemCount()
 * @method static mixed getItems(int $offset = 0, ?int $count = null, ?string $orderBy = null, string $orderDirection = "ASC")
 * @method static array getItem($id)
 * @method static array getActionsForCurrentUser()
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

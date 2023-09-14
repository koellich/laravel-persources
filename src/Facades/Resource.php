<?php

namespace Koellich\Persources\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed list()
 * @method static mixed view($id)
 * @method static mixed create(array $values)
 * @method static mixed update($id, array $values)
 * @method static mixed delete($id)
 * @method static string getModelClassName()
 * @method static array getPermissions()
 * @method static int getItemCount()
 * @method static mixed getItems(int $offset = 0, ?int $count = null, ?string $orderBy = null, string $orderDirection = "ASC")
 * @method static array getItem($id)
 * @method static array getActionsForCurrentUser()
 * @method static string translatedModelName()
 * @method static string translatedModelNamePlural()
 * @method static array columnDefinitions()
 * @method static array datasets()
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

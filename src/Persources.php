<?php

namespace Koellich\Persources;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;

class Persources
{
    protected array $resources = [];

    /**
     * Processes all Resources in the persources.namespace and binds them to the App container using as keys the
     * permissions handled by the respective resource.
     *
     * @return void
     */
    public function registerResources()
    {
        $this->resources = [];
        $path = $this->getResourcesPath();
        if (File::exists($path)) {
            foreach (File::allFiles($path) as $file) {
                $class = config('persources.resources_namespace').'\\'.basename($file, '.php');
                $resource = new $class();
                foreach ($resource->getPermissions() as $permission) {
                    App::singleton("Persources.$permission", $class);
                    App::singleton('Persources.'.$resource->name, $class);
                }
                $this->resources[] = $resource;
            }
        }
    }

    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Returns the Resource responsible for handling requests for the given $permission.
     * Prefixes are ignores, i.e. resources may be named like something.cars.edit which will match the
     * resource registered for cars.edit
     *
     * @return resource
     */
    public function getResourceForPermission(string $permission): ?Resource
    {
        return App::make("Persources.$permission");
    }

    /**
     * Returns the Resource by the given $name
     *
     * @param $name string The Resource's name
     */
    public function getResourceByName(string $name): ?Resource
    {
        return App::make("Persources.$name");
    }

    public function getResourcesPath(): string
    {
        return app_path(Str::of(config('persources.resources_namespace'))
            ->replace('App\\', '')
            ->replace('\\', '/')
        );
    }

    public function getViewsPath(): string
    {
        return resource_path(config('persources.view_root'));
    }

    public function checkPermission(string $permission): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }
        $adminRole = config('persources.admin_role');
        if ($adminRole && $user->hasRole($adminRole)) {
            return true;
        }
        try {
            $user->getAllPermissions()->sole(fn ($p) => $p->name == $permission);

            return true;
        } catch (ItemNotFoundException) {
            return false;
        }
    }

    /**
     * @return string The action part of the permission. E.g. For 'cars.list' the result would be 'list'
     */
    public function getAction(string $permission): string
    {
        return Str::of($permission)->afterLast('.');
    }

    /**
     * Returns an array of actions that are implied by the given $action.
     * E.g. For $action == '...read' the result would be ['...list', '...view']
     *
     * @param  string  $permission
     * @return string
     */
    public function getImpliedActions(string $action): array
    {
        return match ($action) {
            'read' => ['list', 'view'],
            'write' => ['list', 'view', 'create', 'update'],
            default => [$action]
        };
    }

    /**
     * @return string The HTTP action required to perform the action.
     */
    public function getHttpMethod(string $action): string
    {
        return match ($action) {
            'list', 'view' => 'GET',
            'create' => 'POST',
            'update' => 'PATCH',
            'delete' => 'DELETE'
        };
    }

    /**
     * @return string route name
     */
    public function getRouteName(string $permission, string $impliedAction): string
    {
        $hasImpliedActions = in_array($this->getAction($permission), ['read', 'write']);

        return $hasImpliedActions ? "$permission|$impliedAction" : $permission;
    }
}

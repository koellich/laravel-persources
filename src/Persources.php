<?php

namespace Koellich\Persources;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
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
                    App::singleton($permission, $class);
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
    public function getResourceFor($permission): ?Resource
    {
        return App::make($permission);
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

    /**
     * @param string $permission
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
     * @param string $permission
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
     * @param string $permission
     * @return string route name
     */
    public function getRouteName(string $permission, string $impliedAction): string
    {
        $hasImpliedActions = in_array($this->getAction($permission), ['read', 'write']);
        return $hasImpliedActions ? "$permission|$impliedAction" : $permission;
    }

}

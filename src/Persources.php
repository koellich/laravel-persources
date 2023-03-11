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
    public function registerResources() {
        $this->resources = [];
        $path = $this->getResourcesPath();
        if (File::exists($path)) {
            foreach (File::allFiles($path) as $file) {
                $class = config("persources.namespace") . "\\" . basename($file, ".php");
                ray($class);
                $resource = new $class();
                foreach ($resource->getPermissions() as $permission) {
                    App::singleton($permission, $resource);
                }
                $this->resources[] = $resource;
            }
        }
    }

    public function getResources() {
        return $this->resources;
    }

    /**
     * Returns the Resource responsible for handling requests for the given $permission.
     *
     * @param $permission
     * @return Resource
     */
    public function getResourceFor($permission): ?Resource {
        return App::make($permission);
    }

    public function getResourcesPath():string {
        return app_path(Str::of(config("persources.resources_namespace"))
            ->replace("App\\", "")
            ->replace("\\", "/")
        );
    }

    public function getAction(string $permission): string
    {
        $parts = explode(".", $permission);
        return $parts[array_key_last($parts)];
    }
}
